import axios from 'axios';
import Alpine from "alpinejs"
import './bootstrap';
import persist from '@alpinejs/persist';
import interact from 'interactjs';

//Alpinejs
Alpine.plugin(persist)

window.Alpine = Alpine
window.interact = interact;

Alpine.store("sidebar", {
    open: Alpine.$persist(true)
})



//? Quiz Player
Alpine.data("quizPlayer", () => ({
    active: 0,
    answers: {}, // [questionId]: answerId   1(index)-8 , 

    durationMinutes: 0,
    startTime: 0,
    countdownText: "Time: --:--",
    intervalId: null,

    init() {

        const timerElement = this.$el;
        const initialDuration = timerElement.dataset.durationMinutes;
        const initialStartTime = timerElement.dataset.startTime;
        const isNewAttempt = timerElement.dataset.isNew === 'true';

        if (isNewAttempt) {
            localStorage.removeItem('quiz-state');
        }


        const storedState = JSON.parse(localStorage.getItem("quiz-state"));
        this.durationMinutes = parseInt(storedState?.durationMinutes) || parseInt(initialDuration);
        this.startTime = parseInt(storedState?.startTime) || parseInt(initialStartTime);

        this.active = storedState?.active || 0;
        this.answers = storedState?.answers || {};

        this.startTimer();
        this.saveState();

        this.$watch("answers", () => { this.saveState(); })
        this.$watch("active", () => { this.saveState(); })

    },

    saveState() {
        const state = {
            active: this.active,
            answers: this.answers,
            durationMinutes: this.durationMinutes,
            startTime: this.startTime
        };
        localStorage.setItem("quiz-state", JSON.stringify(state));
    },

    /*
    * Geri Sayım 
    ? setInterval() => belli aralıklarla bir fonksiyonu tekrar tekrar çalıştırır (ör. her 1 saniyede 1) bu fonksiyon bir id döndürür bu id = intervalId dir 
    ? clearInterval(this.intervalId) => setInterval döngüsünü durdurur , parametre olarak girilen intervalId ye ait olan döngüyü durdurur.
    ? endTimeInSeconds => quizin başlatıldığı zaman + quizin önceden belirlenen süresi (startTime + durationMinutes * 60) hesaplanarak  sınavın ne zaman biteceği saniye cinsinden hesaplanır
    ? currentTimeInSeconds => saniye cinsinden şu anki zamanı bulur Date.now / 1000
    ? timeLeftInSeconds => sınavın bitiş zamanından şuan ki zaman her saniyede 1 çıkartılır (interval) Her saniye, o anki zamanı bitiş zamanından çıkararak ne kadar süre kaldığını hesaplar
    ? eğer kalan zaman 0 küçük eşit ise süre dolmuştur . interval döngüsü durdurulur ve form submit edilir
    ? ayrıca kalan zaman gösterilmek üzere countdownText'e yazdırılır , her saniye güncellenerek

    */


    startTimer() {
        const endTimeInSeconds = this.startTime + (this.durationMinutes * 60);
        const totalDurationInSeconds = this.durationMinutes * 60;
        this.intervalId = setInterval(() => {
            const currentTimeInSeconds = Math.floor(Date.now() / 1000);

            // Başlangıçtan bu yana geçen süreyi hesapla
            const elapsedTimeInSeconds = currentTimeInSeconds - this.startTime;

            // Kalan süreyi hesapla (Toplam Süre - Geçen Süre)
            const timeLeftInSeconds = totalDurationInSeconds - elapsedTimeInSeconds;

            if (timeLeftInSeconds <= 0) {
                this.countdownText = "Süre Doldu !";
                clearInterval(this.intervalId);
            }

            const minutes = Math.floor(timeLeftInSeconds / 60);   // ör: 2min15sec 
            const seconds = timeLeftInSeconds % 60;

            this.countdownText = `Time: ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        }, 1000);
    },


    toggle(qid, aid) {
        if (this.answers[qid] == aid) {
            delete this.answers[qid];
        }
        else {
            this.answers[qid] = aid;
        }
    },
    prev() {
        if (this.active > 0) {
            this.active--
        }
    },
    next(total) {
        if (this.active < total) {
            this.active++
        }
    },
    async submitQuiz(checkUrl, token) {
        console.log(this.answers);
        const answerData = this.answers;
        try {

            const result = await axios.post(checkUrl, answerData, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                withCredentials: true // session
            });
            console.log(result.data);
            localStorage.removeItem("quiz-state");
            window.location.href = result.data.redirect;
        }
        catch (error) {
            console.log(error)
        }
    }


}));

//? Quiz Create
Alpine.data("quizCreate", (props = {}) => ({

    token: props.token || '',
    negativeMarkingEnabled: false,
    fileName: "",
    fileUrl: "",
    errors: props.errors || {},

    sourceFileName: null,
    sourceFile: null,
    aiLoading: false,


    async submitQuiz(targetUrl) {

        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'İşlem yapılıyor, lütfen bekleyin...', type: 'info' } }));

        let formElement = document.getElementById('quiz-create-form');
        let formData = new FormData(formElement);

        if (this.sourceFile) {
            formData.append('source_file', this.sourceFile);
        }

        if (!this.negativeMarkingEnabled) {
            formData.delete('wrong_to_correct_ratio');
            formData.append('wrong_to_correct_ratio', 0);
        }

        try {
            // 3. Axios İsteği
            const response = await axios.post(targetUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': this.token
                }
            });

            if (response.data.success) {
                // 4. Başarılıysa Bildirim ve Yönlendirme
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: response.data.message, type: 'success' } }));

                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 1000);
            }

        } catch (error) {
            console.error(error);
            // 5. Hata Yönetimi
            if (error.response && error.response.status === 422) {
                // Laravel Validasyon Hatası
                this.errors = error.response.data.errors;
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen hatalı alanları kontrol edin.', type: 'error' } }));
            } else {
                // Genel Hata
                let msg = error.response?.data?.message || 'Bir hata oluştu.';
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
            }
            // Hata alınca loading'i kapat
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
        }
    },


    setSourceFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.sourceFileName = file.name;
            this.sourceFile = file;
        } else {
            this.sourceFileName = null;
            this.sourceFile = null;
        }
    },

    hasError(field) {
        if (this.errors[field]) return true;

        else return false;
    },

    getError(field) {

        if (this.hasError(field)) return this.errors[field][0]

        else return "";
    }


}))


//? Question Create
Alpine.data("questionCreate", (props = {}) => ({
    total_questions: props.number_of_questions ?? 0,
    total_options: props.number_of_options ?? 2,
    current_q_index: 0,
    quizId: props.quizId,
    errors: [],
    sourceFileName: null, // Sidebar PDF adı
    aiLoading: false,

    questions: Array.from({ length: props.number_of_questions ?? 0 }, () => ({
        title: null,
        content: "",
        point: 1,
        img_url: null,
        fileName: "",
        fileUrl: "",
        answers: Array.from({ length: props.number_of_options ?? 2 }, () => ({
            answer_content: "",
            is_correct: false,
        })),
    })),

    nextQuestion() {
        if (this.current_q_index < this.total_questions - 1) {
            this.current_q_index += 1;
        }
    },
    prevQuestion() {
        if (this.current_q_index > 0) {
            this.current_q_index -= 1;
        }
    },

    goToQuestion(index) {
        if (index >= 0 && index < this.total_questions) {
            this.current_q_index = index;
        }
    },

    setFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.questions[this.current_q_index].fileName = file.name;
            this.questions[this.current_q_index].fileUrl = URL.createObjectURL(file);
            this.questions[this.current_q_index].img_url = file;
        }
    },

    hasError(field) {
        const errorKey = `questions.${this.current_q_index}.${field}`;
        console.log(errorKey)
        return this.errors[errorKey] && this.errors[errorKey].length > 0 //errors["questions.0.title"]
    },

    getError(field) {
        const errorKey = `questions.${this.current_q_index}.${field}`;
        if (this.hasError(field)) {
            const idx = this.errors[errorKey][0].search("field");
            const errText = this.errors[errorKey][0].slice(idx, this.errors[errorKey][0].length)

            return this.hasError(field) ? errText : ''; // The questions.0.title field is required. 
            //The questions.0.answers.0.answer_content field is required.
        }
    },


    async submitForm(checkUrl, token) {
        let formData = new FormData();

        /*
            Laravel array parse edebilmesi için
            questions[index][field] şeklinde gönderiyoruz
            örneğin ; 

            "questions" => [
                0 => [
                    "title" => "Soru 1",
                    "content" => "Soru 1 içeriği"
                ],
                1 => [
                    "title" => "Soru 2",
                    "content" => "Soru 2 içeriği"
                ],

        */
        formData.append("quizId", this.quizId)
        this.questions.forEach((q, idx) => {
            formData.append(`questions[${idx}][content]`, q.content);
            formData.append(`questions[${idx}][points]`, q.point);
            if (q.img_url) {
                formData.append(`questions[${idx}][img_url]`, q.img_url);
            }
            if (q.title) {
                formData.append(`questions[${idx}][title]`, q.title);
            }
            q.answers.forEach((a, a_idx) => {
                formData.append(`questions[${idx}][answers][${a_idx}][answer_content]`, a.answer_content);
                // .append metodu değer otomatik string çevirdiği için bool da sorun oluşturmasın diye 1,0 şeklinde gönderiyorum
                const isCorrectValue = a.is_correct ? 1 : 0;
                formData.append(`questions[${idx}][answers][${a_idx}][is_correct]`, isCorrectValue)
            })
        })

        console.log(formData)
        try {
            const result = await axios.post(checkUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': token,
                },
            });
            console.log(result.data);
            window.location.href = result.data.redirect;
        } catch (error) {
            console.log("Error : ", error.response.data);
            if (error.response && error.response.status === 422) {
                this.errors = error.response.data.errors;
                console.log("errors : ", this.errors);
            }
        }

    },
    async generateSingleQuestionAI() {
        if (!this.sourceFileName && !this.questions[this.current_q_index].title) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen önce döküman yükleyin veya bir konu başlığı girin!', type: 'warning' } }));
            return;
        }

        this.aiLoading = true;

        try {

            // simülasyon örnek öylesine
            setTimeout(() => {
                this.questions[this.current_q_index].title = "AI Tarafından Üretilen Başlık";
                this.questions[this.current_q_index].content = "Bu soru yapay zeka tarafından döküman analiz edilerek oluşturulmuştur.";
                this.aiLoading = false;
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Soru başarıyla üretildi! ✨', type: 'success' } }));
            }, 1500);

        } catch (error) {
            console.error(error);
            this.aiLoading = false;
        }
    }


}))

//? Profile Avatar
Alpine.data("profileAvatar", (props = {}) => ({

    previewUrl: props.initialUrl || '',
    token: props.csrf_token || '',
    isUploading: false,

    async updateAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;
        this.previewUrl = URL.createObjectURL(file);
        this.isUploading = true;

        const formData = new FormData();
        formData.append('avatar_img', file);

        try {
            const response = await axios.post('/profile/update-avatar', formData, {
                headers: {
                    'X-CSRF-TOKEN': this.token
                },
            });

            console.log('Avatar güncellendi:', response.data);

        } catch (error) {
            console.error(error);
            alert('Resim yüklenirken bir sorun oluştu.');
            this.previewUrl = "";
        } finally {
            this.isUploading = false;
        }

    }

}))


//? Exam Canvas
Alpine.data("examCanvas", (props = {}) => ({
    token: props.token || '',

    //pdf viewer
    showPreviewModal: false,
    previewUrl: null,
    iframeLoading: false,

    elements: props.initialElements || [],
    examTitle: props.examTitle || 'Yeni Sınav Kağıdı',
    examId: props.examId || null,

    isSaved: false,
    showTitleModal: false,
    tempTitle: '',

    selectedId: null,
    draggingType: null,
    draggingPayload: null,
    cursorMode: 'select',


    activePage: 1,
    totalPages: 1,

    aiTargetId: null,
    aiPrompt: '',
    aiContext: '',
    aiFile: null,
    aiDifficulty: 'medium',


    aiModalOpen: false,
    aiOptionCount: 5,
    activeAiItem: null,
    aiBatchModalOpen: false,


    aiRequests: [],
    aiPoolGroups: [],
    aiLoading: false,

    isLoading: false,
    pendingAction: null, // İndir, Ön İzle veya Kütüphane eylemini hafızada tutmak için
    tempCategories: [],
    tempDescription: '',
    initialCategories: props.initialCategories || [], // Blade'den gelen (Config değil props kullanıyoruz)
    initialDescription: props.initialDescription || '',
    allCategories: props.allCategories || [],


    get currentPageElements() {
        if (!Array.isArray(this.elements)) return [];
        return this.elements.filter(el => el.page === this.activePage);
    },

    get selectedItem() {
        return this.elements.find(el => el.id === this.selectedId);
    },


    init() {

        this.elements = this.elements.filter(el => el.id && el.page);
        this.tempTitle = this.examTitle;
        this.tempDescription = this.initialDescription;
        // Gelen kategori objelerini [1, 3] gibi ID listesine çevir
        if (Array.isArray(this.initialCategories)) {
            this.tempCategories = this.initialCategories.map(c => c.id);
        }

        if (this.elements.length > 0) {
            const maxPage = Math.max(...this.elements.map(el => el.page || 1));
            this.pages = Array.from({ length: maxPage }, (_, i) => i + 1);
            this.totalPages = maxPage;
        }
        else {
            setTimeout(() => {
                this.addItem('header_block', 400, 80);
                this.addItem('student_info', 400, 200);
            }, 300);
        }

        window.addEventListener('beforeunload', (e) => {
            if (this.elements.length > 0 && !this.isSaved) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        this.$nextTick(() => {
            this.setupInteract();
        });
    },


    setupInteract() {
        if (typeof interact === 'undefined') return;
        const self = this;

        interact('.draggable-item')
            .draggable({
                ignoreFrom: 'input, textarea, button, select, .no-drag',
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: false,
                        elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
                    })
                ],
                listeners: {
                    move(event) {
                        if (self.cursorMode !== 'select' && self.cursorMode !== 'move') return;
                        const id = parseFloat(event.target.id);
                        const item = self.elements.find(el => el.id === id);
                        if (item) { item.x += event.dx; item.y += event.dy; }
                    }
                }
            })
            .resizable({
                ignoreFrom: '.no-drag',
                edges: { left: true, right: true, bottom: true, top: true },
                modifiers: [interact.modifiers.restrictEdges({ outer: 'parent' }), interact.modifiers.restrictSize({ min: { width: 50, height: 20 } })],
                listeners: {
                    move(event) {
                        if (self.cursorMode !== 'select' && self.cursorMode !== 'move') return;
                        const id = parseFloat(event.target.id);
                        const item = self.elements.find(el => el.id === id);
                        if (item) {
                            item.w = event.rect.width; item.h = event.rect.height;
                            item.x += event.deltaRect.left; item.y += event.deltaRect.top;
                        }
                    }
                }
            })
            .on('down', function (event) {
                if (event.target.closest('.no-drag') || event.target.closest('input') || event.target.closest('textarea')) {
                    return;
                }
                if (self.cursorMode === 'select' || self.cursorMode === 'move') {
                    const target = event.target.closest('.draggable-item');
                    if (target) {
                        self.select(target.id);
                    }
                }
            });
    },


    dragStart(event, type, groupIndex) {
        this.draggingType = type;
        if (groupIndex !== undefined && this.aiPoolGroups[groupIndex]) {
            this.draggingPayload = JSON.parse(JSON.stringify(this.aiPoolGroups[groupIndex].questions[0]));
            event.dataTransfer.setData('groupIndex', groupIndex);
        } else {
            this.draggingPayload = null;
        }
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('text/plain', type);
    },

    handleDrop(event) {
        const paper = document.getElementById('paper');
        const rect = paper.getBoundingClientRect();
        let x = event.clientX - rect.left;
        let y = event.clientY - rect.top;

        // Varsayılan genişlik (Yükseklik önemsiz, autoResize halledecek)
        let currentWidth = 700;

        if (this.draggingType) {

            // 1. AI Verisi Varsa İşle
            if (this.draggingPayload) {
                // Numara güncelle
                const existingCount = this.elements.filter(e => e.type === this.draggingType).length;
                this.draggingPayload.number = (existingCount + 1) + '.';

                // Şık temizliği
                if (this.draggingPayload.options && Array.isArray(this.draggingPayload.options)) {
                    this.draggingPayload.options = this.draggingPayload.options.map(opt => opt.replace(/^[A-Z0-9][).]\s*/, ''));
                }
                // D/Y Formatı
                if (this.draggingType === 'true_false') {
                    this.draggingPayload.format = 'D / Y';
                }
            }

            // 2. Sınır Kontrolleri
            const paperWidth = paper.offsetWidth;
            const paperHeight = paper.offsetHeight; // (Height kontrolü çok kasmamıza gerek yok artık)

            if (x < 0) x = 0; if (y < 0) y = 0;
            if (x + currentWidth > paperWidth) x = paperWidth - currentWidth;

            // 3. Ekle
            this.addItem(this.draggingType, x, y, this.draggingPayload);

            // 4. Boyutu ve Genişliği Ayarla (Yükseklik autoResize ile düzelecek ama varsayılan verelim)
            const lastItem = this.elements[this.elements.length - 1];
            if (lastItem) {
                lastItem.w = currentWidth;
                lastItem.h = 100; // Geçici değer, render olunca düzelecek
            }

            // 5. Havuzdan Sil
            const groupIndex = event.dataTransfer.getData('groupIndex');
            if (groupIndex !== '' && this.aiPoolGroups[groupIndex] && this.aiPoolGroups[groupIndex].questions) {
                this.aiPoolGroups[groupIndex].questions.shift();
                this.aiPoolGroups[groupIndex].count--;
                if (this.aiPoolGroups[groupIndex].count <= 0) {
                    this.aiPoolGroups.splice(groupIndex, 1);
                }
            }

            this.draggingType = null;
            this.draggingPayload = null;
        }
    },


    addItem(type, x = 50, y = 50, preFilledContent = null) {
        let width = 200, height = 50, content = {};

        let styles = {
            fontSize: 14,
            color: '#000000',
            fontWeight: 'normal',
            textAlign: 'left',
            zIndex: 1,
            borderWidth: 0,
            borderColor: '#000000',
            backgroundColor: 'transparent',
            borderRadius: 0
        };


        if (type === 'header_block') {
            width = 600;
            height = 100;
            content = {
                title: 'ATATÜRK ÜNİVERSİTESİ',
                faculty: 'Mühendislik Fakültesi',
                term: '2025-2026 Güz Dönemi'
            };
            styles.textAlign = 'center';
            styles.fontWeight = 'bold';
        }
        else if (type === 'student_info') {
            width = 700;
            height = 80;
            styles.borderWidth = 0;
            styles.backgroundColor = 'transparent';

            content = {
                label1: 'Adı Soyadı:', val1: '',
                label2: 'Numara:', val2: '',
                label3: 'Sınıfı:', val3: '',
                label4: 'Puan:', val4: ''
            };
        }
        else if (type === 'multiple_choice') {
            width = 700;
            height = 180;
            content = {
                number: '1.',
                question: 'Soru metnini buraya giriniz...',
                point: '10',
                options: ['Seçenek A metni', 'Seçenek B metni', 'Seçenek C metni', 'Seçenek D metni', 'Seçenek E metni']
            };
        }
        else if (type === 'open_ended') {
            width = 700;
            height = 120;
            content = {
                number: '2.',
                question: 'Klasik soru metnini buraya giriniz...',
                point: '20'
            };
        }
        else if (type === 'fill_in_blanks') {
            width = 700;
            height = 80;
            content = {
                number: '3.',
                question: 'Boşluk doldurma sorusu...',
                point: '5'
            };
        }
        else if (type === 'true_false') {
            width = 700;
            height = 50;
            content = {
                number: '4.',
                question: 'Doğru yanlış sorusu...',
                point: '5',
                format: 'D / Y'
            };
        }
        else if (type === 'custom_question') {
            width = 400;
            height = 200;
            styles.borderWidth = 1;
            styles.borderColor = '#e5e7eb';
            content = { text: 'Özel Soru Alanı' };
        }
        else if (type === 'heading') {
            content = 'Ana Başlık';
            width = 300;
            height = 50;
            styles.fontSize = 24;
            styles.fontWeight = 'bold';
        }
        else if (type === 'sub_heading') {
            content = 'Alt Başlık';
            width = 250;
            height = 40;
            styles.fontSize = 18;
            styles.fontWeight = 'bold';
            styles.color = '#555';
        }
        else if (type === 'text') {
            content = 'Metin...';
            width = 200;
            height = 40;
        }
        else if (type === 'image') {
            width = 200;
            height = 200;
            content = '';
        }
        else if (type === 'box') {
            width = 150;
            height = 150;
            styles.borderWidth = 2;
        }

        if (preFilledContent) {
            content = JSON.parse(JSON.stringify(preFilledContent));
            if (type === 'multiple_choice') { width = 700; height = 150; }
            if (type === 'open_ended') { width = 700; height = 120; }
            if (type === 'fill_in_blanks') { width = 700; height = 60; }
            if (type === 'true_false') { width = 700; height = 50; }
        }

        x = x - (width / 2);
        y = y - (height / 2);

        const paper = document.getElementById('paper');
        if (paper) {
            const paperW = paper.offsetWidth;
            const paperH = paper.offsetHeight;

            if (x < 0) x = 0;
            if (y < 0) y = 0;

            if (x + width > paperW) x = paperW - width;
            if (y + height > paperH) y = paperH - height;
        }

        this.elements.push({
            id: Date.now() + Math.random(),
            page: this.activePage,
            type: type,
            content: content,
            x: x,
            y: y,
            w: width,
            h: height,
            styles: styles
        });
        const newItem = this.elements[this.elements.length - 1];
        this.selectedId = newItem.id;
    },


    addAiRequest() {

        this.aiRequests.push({ type: 'multiple_choice', count: 1, difficulty: 'medium', option_count: 4 });
    },

    removeAiRequest(index) { this.aiRequests.splice(index, 1); },


    async generateBatchAi() {
        // Validasyon
        if (this.aiRequests.length === 0) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'En az bir kural eklemelisiniz.', type: 'warning' } }));
            return;
        }
        if (!this.aiPrompt && !this.aiContext && !this.aiFile) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen bir konu, metin veya dosya girin.', type: 'warning' } }));
            return;
        }

        this.aiLoading = true;

        let formData = new FormData();
        formData.append('prompt', this.aiPrompt);
        formData.append('context', this.aiContext);
        if (this.aiFile) formData.append('file', this.aiFile);

        // Kuralları JSON string olarak gönderiyoruz
        formData.append('rules', JSON.stringify(this.aiRequests));

        try {

            const response = await axios.post('/exam/ai-batch-generate', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': this.token
                }
            });

            if (response.data.success && response.data.data.groups) {

                // Gelen veriyi işle ve Havuza (aiPoolGroups) ekle
                response.data.data.groups.forEach(group => {

                    // --- TEMİZLİK İŞLEMİ BURADA BAŞLIYOR ---
                    // Gelen soruları döngüye alıp tek tek temizliyoruz
                    group.questions.forEach(q => {
                        // 1. Soru metnini temizle (Yıldızları sil)
                        q.question = this.cleanText(q.question);

                        // 2. Şıklar varsa onları da temizle
                        if (q.options && Array.isArray(q.options)) {
                            q.options = q.options.map(opt => {
                                // Önce A) B) gibi ön ekleri sil
                                let noPrefix = opt.replace(/^[A-Z0-9][).]\s*/, '');
                                // Sonra Yıldızları sil
                                return this.cleanText(noPrefix);
                            });
                        }
                    });
                    // --- TEMİZLİK İŞLEMİ BİTTİ ---


                    // Tip ismini Türkçe label'a çevir
                    let typeLabel = 'Bilinmeyen';
                    if (group.type === 'multiple_choice') typeLabel = 'Çoktan Seçmeli';
                    if (group.type === 'open_ended') typeLabel = 'Klasik';
                    if (group.type === 'true_false') typeLabel = 'Doğru/Yanlış';
                    if (group.type === 'fill_in_blanks') typeLabel = 'Boşluk Doldurma';

                    // Havuza Ekle (Artık temizlenmiş 'group.questions' ekleniyor)
                    this.aiPoolGroups.push({
                        id: Date.now() + Math.random(),
                        type: group.type,
                        typeName: typeLabel,
                        difficulty: group.difficulty,
                        difficultyLabel: group.difficulty.toUpperCase(),
                        count: group.questions.length,
                        questions: group.questions // <-- Buraya temizlenmiş hali gidiyor
                    });
                });

                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Sorular havuza eklendi! Sürükleyip kullanabilirsiniz.', type: 'success' } }));
                this.aiBatchModalOpen = false;
            }

        } catch (error) {
            console.error(error);
            let msg = error.response?.data?.message || 'Bir hata oluştu.';
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
        } finally {
            this.aiLoading = false;
        }
    },


    openAiModal(item) { this.activeAiItem = item; this.aiModalOpen = true; this.aiPrompt = ''; this.aiContext = ''; this.aiFile = null; },



    cleanText(text) {
        if (!text) return '';
        let cleaned = text;

        // 1. Kalın Yazıları Temizle (**Yazı** -> Yazı)
        cleaned = cleaned.replace(/\*\*([^*]+)\*\*/g, '$1');

        // 2. İtalik Yazıları Temizle (*Yazı* -> Yazı)
        // Matematiksel çarpma (3 * 5) işaretine dokunmaz.
        cleaned = cleaned.replace(/(^|\s)\*([^\s*]+)\*(\s|$|[.,:?!])/g, '$1$2$3');

        return cleaned.trim();
    },


    async generateAiContent() {
        // 1. Validasyonlar
        if (!this.activeAiItem) return;

        // Konu, Metin veya Dosya yoksa uyarı ver
        if (!this.aiPrompt && !this.aiContext && !this.aiFile) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen bir konu, metin veya dosya girin.', type: 'warning' } }));
            return;
        }


        this.aiLoading = true;

        // 2. FormData Hazırlığı
        let formData = new FormData();
        formData.append('prompt', this.aiPrompt);
        formData.append('context', this.aiContext);
        if (this.aiFile) formData.append('file', this.aiFile);

        // 3. TEK BİR KURAL OLUŞTUR (Mevcut controller yapısına uymak için)
        // Seçili kutunun tipine göre backend'e "Bana bundan 1 tane üret" diyoruz.
        let rule = {
            type: this.activeAiItem.type,
            count: 1,
            difficulty: this.aiDifficulty
        };

        // Eğer çoktan seçmeli ise şık sayısını belirtelim (Varsayılan 5 şık)
        if (this.activeAiItem.type === 'multiple_choice') {
            rule.option_count = parseInt(this.aiOptionCount) || 5;
        }

        // Kuralları JSON string olarak ekle (Backend böyle bekliyor)
        formData.append('rules', JSON.stringify([rule]));

        try {
            // 4. İSTEK GÖNDER (Mevcut Controller Rotası)
            const response = await axios.post('/exam/ai-batch-generate', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': this.token
                }
            });

            // 5. CEVABI İŞLE
            if (response.data.success &&
                response.data.data.groups &&
                response.data.data.groups.length > 0 &&
                response.data.data.groups[0].questions.length > 0) {

                // Backend'den gelen ilk (ve tek) soruyu al
                const generatedData = response.data.data.groups[0].questions[0];
                const item = this.activeAiItem;

                // --- İÇERİĞİ GÜNCELLE ---

                // Soru Metni
                item.content.question = this.cleanText(generatedData.question);

                // Puan (Backend gönderiyorsa al, yoksa eskisi kalsın)
                if (generatedData.point) item.content.point = generatedData.point;

                // Çoktan Seçmeli Şıkları
                if (item.type === 'multiple_choice' && Array.isArray(generatedData.options)) {
                    item.content.options = generatedData.options.map(opt => {
                        // 1. A) B) kısmını sil
                        let noPrefix = opt.replace(/^[A-Z0-9][).]\s*/, '');
                        // 2. Yıldızları (*) sil ve geri döndür
                        return this.cleanText(noPrefix);
                    });
                    // Buraya 'return' koymuyoruz! Kod akmaya devam etmeli.
                }

                // --- BİTİŞ İŞLEMLERİ ---

                // Modalı Kapat
                this.aiModalOpen = false;

                // Bildirim Ver
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Soru başarıyla güncellendi!', type: 'success' } }));

                // Inputları Temizle
                this.aiPrompt = '';
                this.aiContext = '';
                this.aiFile = null;
                // Dosya inputunu da HTML'den resetle
                const fileInput = document.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = '';
                // *** KRİTİK DÜZELTME BURADA ***
                this.$nextTick(() => {
                    const container = document.getElementById(item.id);
                    if (container) {
                        // 1. Kutunun içindeki TÜM metin alanlarını (Soru + Şıklar) bul
                        const textareas = container.querySelectorAll('textarea');

                        // 2. Her bir textarea için autoResize fonksiyonunu çalıştır
                        // Bu sayede hem metin alanları uzar, hem de en sonunda kutu uzar.
                        textareas.forEach(t => {
                            this.autoResize({ target: t }, item);
                        });
                    }
                });

            } else {
                throw new Error('AI içerik üretemedi veya format hatalı.');
            }

        } catch (error) {
            console.error("AI Hatası:", error);
            let msg = error.response?.data?.message || 'AI servisinde hata oluştu.';
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
        } finally {
            this.aiLoading = false;
        }
    },


    setMode(mode) { this.cursorMode = mode; },
    setFile(event) { this.aiFile = event.target.files[0]; },
    addPage() { this.totalPages++; this.activePage = this.totalPages; this.selectedId = null; },
    setPage(pageNum) { this.activePage = pageNum; this.selectedId = null; },
    deletePage() {
        if (this.totalPages > 1) {
            if (confirm('Sayfayı silmek istediğinize emin misiniz?')) {
                this.elements = this.elements.filter(el => el.page !== this.activePage);
                this.elements.forEach(el => { if (el.page > this.activePage) el.page--; });
                this.totalPages--;
                this.activePage = Math.max(1, this.activePage - 1);
            }
        }
    },
    select(id) {

        if (this.cursorMode === 'draw' || this.cursorMode === 'shape') return;

        if (this.cursorMode === 'select' || this.cursorMode === 'move') {
            this.selectedId = id;
        }
    },
    deselect() { this.selectedId = null; },
    remove(id) { this.elements = this.elements.filter(el => el.id !== id); this.selectedId = null; },

    async uploadImage(event, item) {
        const file = event.target.files[0];
        if (!file) return;
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await axios.post('/exam/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': this.token
                }
            });

            if (response.data.success) {

                item.content = response.data.url;

            }

        } catch (error) {
            console.error("Yükleme Hatası:", error);
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'Resim yüklenirken hata oluştu', type: 'error' }
            }));
        } finally {
            event.target.value = '';
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
        }
    },



    saveAndAction(actionType) {
        this.pendingAction = actionType; // Eylemi hafızaya al (örn: 'download')

        // Mevcut verileri modal'a taşı
        this.tempTitle = this.examTitle;

        // Modalı Göster
        this.showTitleModal = true;
    },


    saveTitleAndContinue() {
        if (!this.tempTitle || this.tempTitle.trim() === '') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen geçerli bir isim giriniz!', type: 'warning' } }));
            return;
        }

        // Değişkenleri güncelle
        this.examTitle = this.tempTitle;

        // Modalı kapat
        this.showTitleModal = false;

        // GERÇEK KAYIT FONKSİYONUNU ÇAĞIR
        this.saveExamToDatabase();
    },

    startResize(event, item) {
        const startX = event.clientX;
        const startY = event.clientY;
        const startWidth = parseInt(item.w);
        const startHeight = parseInt(item.h);

        const onMouseMove = (e) => {
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            // Minimum boyut sınırı (50x30)
            item.w = Math.max(50, startWidth + dx);
            item.h = Math.max(30, startHeight + dy);
        };

        const onMouseUp = () => {
            window.removeEventListener('mousemove', onMouseMove);
            window.removeEventListener('mouseup', onMouseUp);
        };

        window.addEventListener('mousemove', onMouseMove);
        window.addEventListener('mouseup', onMouseUp);
    },

    // 3. ADIM: Veritabanı İşlemi (Eski saveAndAction kodun buraya taşındı ve güncellendi)
    async saveExamToDatabase() {
        let actionType = this.pendingAction; // Hafızadaki eylemi al

        let actionMessage = 'İşlem yapılıyor...';
        if (actionType === 'download') actionMessage = 'PDF İndiriliyor...';
        else if (actionType === 'preview') actionMessage = 'Ön İzleme Hazırlanıyor...';
        else if (actionType === 'library') actionMessage = 'Kütüphaneye Dönülüyor...';

        this.isLoading = true;
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
        window.dispatchEvent(new CustomEvent('notify', { detail: { message: `Kaydediliyor ve ${actionMessage}`, type: 'info' } }));

        try {
            const url = this.examId ? `/exam/update/${this.examId}` : '/exam/save';

            // --- GÜNCELLENEN PAYLOAD (Kategori ve Açıklama eklendi) ---
            const payload = {
                title: this.examTitle,
                elements: this.elements,
                page_count: this.totalPages || 1,
                // Yeni alanlar:
                categories: this.tempCategories,
                description: this.tempDescription,
                is_public: false // Editörden kaydederken hep false
            };
            // ----------------------------------------------------------

            const response = await axios.post(url, payload, {
                headers: { 'X-CSRF-TOKEN': this.token, 'Content-Type': 'application/json' }
            });

            if (response.data.success) {

                if (!this.examId && response.data.id) {
                    this.examId = response.data.id;
                    window.history.pushState({}, '', `/exam/edit/${this.examId}`);
                }

                this.isSaved = true;

                // --- AKSİYONLAR ---

                // PDF İNDİR
                if (actionType === 'download') {
                    const link = document.createElement('a');
                    link.href = `/exam/${this.examId}/download`;
                    link.setAttribute('download', '');
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'PDF iniyor! 📄', type: 'success' } }));
                }

                // ÖN İZLEME 
                else if (actionType === 'preview') {
                    this.iframeLoading = true;
                    this.previewUrl = `/exam/${this.examId}/preview?t=${new Date().getTime()}`;
                    this.showPreviewModal = true;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Ön izleme açıldı!', type: 'success' } }));
                }

                // KÜTÜPHANE
                else if (actionType === 'library') {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Kütüphaneye gidiliyor...', type: 'success' } }));
                    setTimeout(() => {
                        window.location.href = "/library";
                    }, 1000);
                    return;
                }

                // HİÇBİR ŞEY YOKSA (Sadece Kaydet dediyse)
                else {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Başarıyla Kaydedildi!', type: 'success' } }));
                }
            }

        } catch (error) {
            console.error("Hata:", error);
            let msg = error.response?.data?.message || 'Bir hata oluştu.';
            if (error.response?.status === 419) msg = 'Oturum süreniz dolmuş, sayfayı yenileyin.';
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
        } finally {
            if (actionType !== 'library') {
                window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
            }
            this.isLoading = false;
            this.pendingAction = null; // Aksiyonu sıfırla
        }
    },


    returnToPool(id) {
        // 1. Ögeyi bul
        const item = this.elements.find(el => el.id === id);
        if (!item) return;

        // 2. İKONLARIN ÇALIŞMASI İÇİN TİP HARİTASI (HTML ile birebir aynı olmalı)
        const typeMap = {
            'multiple_choice': 'Çoktan Seçmeli',
            'open_ended': 'Klasik',
            'fill_in_blanks': 'Boşluk Doldurma',
            'true_false': 'Doğru/Yanlış'
        };

        // Türkçe ismini al (İkonlar buna göre çıkıyor)
        const typeLabel = typeMap[item.type] || 'Bilinmeyen';

        // 3. Havuzda bu tipte bir grup var mı?
        let group = this.aiPoolGroups.find(g => g.type === item.type);

        // 4. Grup yoksa, ORİJİNAL YAPIYA UYGUN oluştur
        if (!group) {
            group = {
                id: Date.now(),
                type: item.type,          // Örn: 'multiple_choice'
                typeName: typeLabel,      // Örn: 'Çoktan Seçmeli' (İkon için şart!)
                difficulty: 'medium',     // Varsayılan Sarı renk olsun
                difficultyLabel: 'GERİ',  // Etikette 'GERİ' yazsın
                count: 0,
                questions: []
            };
            this.aiPoolGroups.push(group);
        }

        // 5. İçeriği temizle (Canvas koordinatlarını at, sadece soruyu al)
        const rawContent = JSON.parse(JSON.stringify(item.content));

        // (İsteğe bağlı) Soru numarasını temizle ki havuzda "5. Soru..." gibi durmasın
        if (rawContent.number) rawContent.number = '';

        // 6. Gruba ekle (En başa)
        group.questions.unshift(rawContent);
        group.count++;

        // 7. Canvas'tan sil
        this.remove(id);

        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Soru havuza geri taşındı.', type: 'info' } }));
    },

    autoResize(event, item) {
        // Sadece yazı yazılan alanı (textarea/input) bul
        const el = event.target;

        // Eğer bu bir metin kutusuysa, kendi içeriğine göre uzamasını sağla
        if (el && (el.tagName === 'TEXTAREA' || el.tagName === 'INPUT')) {
            el.style.height = 'auto';       // Önce boyu sıfırla (küçülme ihtimaline karşı)
            el.style.height = el.scrollHeight + 'px'; // Sonra içeriğe eşitle
        }

        // DİKKAT: Burada item.h'yi güncellemene gerek YOK.
        // Çünkü HTML tarafında "height: auto" dedik, kutu kendiliğinden büyüyecek.
    },

    toggleCategory(id) {
        if (this.tempCategories.includes(id)) {
            // Varsa çıkar
            this.tempCategories = this.tempCategories.filter(c => c !== id);
        } else {
            // Yoksa ekle
            this.tempCategories.push(id);
        }
    },

    getCategoryName(id) {
        const cat = this.allCategories.find(c => c.id == id);
        return cat ? cat.name : 'Bilinmeyen';
    },


    saveToConsole() { console.log(JSON.stringify(this.elements)); window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Kayıt Başarılı!', type: 'success' } }));; }
}));

//? Library Handler
Alpine.data("libraryHandler", (props = {}) => ({
    activeTab: 'quizzes',
    showPreviewModal: false,
    previewUrl: null,
    iframeLoading: false,

    // ---  ÖN İZLEME FONKSİYONU ---
    openPreview(id) {
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Ön izleme hazırlanıyor...', type: 'info' }
        }));

        this.iframeLoading = true;

        this.previewUrl = `/exam/${id}/preview?t=${new Date().getTime()}`;

        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
            this.showPreviewModal = true;

            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'Ön izleme hazır! 👀', type: 'success' }
            }));
        }, 500);
    },

    // --- İNDİRME FONKSİYONU ---
    downloadPdf(id) {
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'PDF hazırlanıyor ve iniyor...', type: 'info' }
        }));

        const link = document.createElement('a');
        link.href = `/exam/${id}/download`;
        link.setAttribute('download', '');
        link.style.display = 'none';
        document.body.appendChild(link);

        setTimeout(() => {
            link.click();
            document.body.removeChild(link);

            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'İndirme başladı! 📄', type: 'success' }
            }));
        }, 800);
    }
}));

Alpine.start();