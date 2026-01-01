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

    negativeMarkingEnabled: false,
    fileName: "",
    fileUrl: "",
    errors: props.errors || {},

    hasError(field) {
        // hata yoksa undefined dönüyoruz 
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
                console.log("errors : ", this.errors); // Hataları konsolda görebilirsiniz
            }
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

    elements: props.initialElements || [],
    examTitle: props.examTitle || 'Yeni Sınav Kağıdı',
    examId: props.examId || null,

    selectedId: null,
    draggingType: null,
    draggingPayload: null,
    cursorMode: 'select',


    activePage: 1,
    totalPages: 1,


    aiPrompt: '',
    aiContext: '',
    aiFile: null,
    aiDifficulty: 'medium',


    aiModalOpen: false,
    activeAiItem: null,
    aiBatchModalOpen: false,


    aiRequests: [{ type: 'multiple_choice', count: 1, difficulty: 'medium', option_count: 4 }],
    aiPoolGroups: [],
    aiLoading: false,


    get currentPageElements() {
        if (!Array.isArray(this.elements)) return [];
        return this.elements.filter(el => el.page === this.activePage);
    },

    get selectedItem() {
        return this.elements.find(el => el.id === this.selectedId);
    },


    init() {

        this.elements = this.elements.filter(el => el.id && el.page);

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
            // Eğer eleman varsa uyarı göster
            if (this.elements.length > 0) {
                e.preventDefault();
                e.returnValue = ''; // Tarayıcı standart uyarısını tetikler
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
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: false,
                        elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
                    })
                ],
                listeners: {
                    move(event) {
                        if (self.cursorMode !== 'select') return;
                        const id = parseFloat(event.target.id);
                        const item = self.elements.find(el => el.id === id);
                        if (item) { item.x += event.dx; item.y += event.dy; }
                    }
                }
            })
            .resizable({
                edges: { left: true, right: true, bottom: true, top: true },
                modifiers: [interact.modifiers.restrictEdges({ outer: 'parent' }), interact.modifiers.restrictSize({ min: { width: 50, height: 20 } })],
                listeners: {
                    move(event) {
                        if (self.cursorMode !== 'select') return;
                        const id = parseFloat(event.target.id);
                        const item = self.elements.find(el => el.id === id);
                        if (item) {
                            item.w = event.rect.width; item.h = event.rect.height;
                            item.x += event.deltaRect.left; item.y += event.deltaRect.top;
                        }
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

        const paperWidth = paper.offsetWidth;
        const paperHeight = paper.offsetHeight;

        const defaultW = 200;
        const defaultH = 50;

        if (x < 0) x = 0;
        if (y < 0) y = 0;
        if (x + defaultW > paperWidth) x = paperWidth - defaultW;
        if (y + defaultH > paperHeight) y = paperHeight - defaultH;

        if (this.draggingType) {
            this.addItem(this.draggingType, x, y, this.draggingPayload);

            const groupIndex = event.dataTransfer.getData('groupIndex');
            if (groupIndex !== '' && this.aiPoolGroups[groupIndex]) {
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

        // Varsayılan Stiller
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

        // --- TİPE GÖRE AYARLAR ---

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
            // İstersen border'ı tamamen kaldırabilirsin, çünkü içeride tablo yapısı var
            styles.borderWidth = 0;
            styles.backgroundColor = 'transparent';

            content = {
                // Val değerlerini BOŞ bıraktık, böylece noktalar gitti.
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
                number: '1.', // <--- YENİ: Soru Numarası
                question: 'Soru metnini buraya giriniz...',
                point: '10',
                // A), B) yazılarını kaldırdık, HTML otomatik koyacak
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
            height = 60;
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
                format: 'D / Y' // <--- GÜNCELLENDİ
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

        // --- PRE-FILLED İÇERİK VARSA YÜKLE ---
        if (preFilledContent) {
            content = JSON.parse(JSON.stringify(preFilledContent));
            // Varsayılan boyutları koru (veya kaydedilen boyutu kullanmak istersen burayı silebilirsin)
            if (type === 'multiple_choice') { width = 700; height = 150; }
            if (type === 'open_ended') { width = 700; height = 120; }
            if (type === 'fill_in_blanks') { width = 700; height = 60; }
            if (type === 'true_false') { width = 700; height = 50; }
        }

        // --- MERKEZLEME HESABI ---
        x = x - (width / 2);
        y = y - (height / 2);

        // --- KAĞIT DIŞINA TAŞMAYI ENGELLE ---
        const paper = document.getElementById('paper');
        if (paper) {
            const paperW = paper.offsetWidth;
            const paperH = paper.offsetHeight;

            if (x < 0) x = 0;
            if (y < 0) y = 0;

            if (x + width > paperW) x = paperW - width;
            if (y + height > paperH) y = paperH - height;
        }

        // --- DİZİYE EKLE ---
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

        // --- YENİ EKLENENİ SEÇ ---
        const newItem = this.elements[this.elements.length - 1];
        this.selectedId = newItem.id;
    },


    addAiRequest() {

        this.aiRequests.push({ type: 'multiple_choice', count: 1, difficulty: 'medium', option_count: 4 });
    },

    removeAiRequest(index) { this.aiRequests.splice(index, 1); },


    generateBatchAi() {
        this.aiLoading = true;
        setTimeout(() => {
            const sourceSummary = (this.aiPrompt ? `Konu: "${this.aiPrompt}". ` : '') + (this.aiContext ? `Metin Verildi. ` : '') + (this.aiFile ? `Dosya: ${this.aiFile.name}` : '');
            this.aiRequests.forEach(req => {
                let generatedQuestions = [];
                let typeName = '';
                if (req.type === 'multiple_choice') typeName = 'Çoktan Seçmeli';
                else if (req.type === 'open_ended') typeName = 'Klasik';
                else if (req.type === 'true_false') typeName = 'Doğru/Yanlış';
                else if (req.type === 'fill_in_blanks') typeName = 'Boşluk Doldurma';

                let difficultyLabel = req.difficulty === 'easy' ? 'Kolay' : (req.difficulty === 'hard' ? 'Zor' : 'Orta');

                for (let i = 0; i < req.count; i++) {
                    let mockContent = {};
                    let points = req.difficulty === 'hard' ? '20' : '10';

                    if (req.type === 'multiple_choice') {
                        let options = [];
                        for (let o = 0; o < (req.option_count || 4); o++) options.push(`Seçenek ${String.fromCharCode(65 + o)}`);
                        mockContent = { question: `(AI) ${sourceSummary} hakkında soru #${i + 1}?`, point: points, options: options };
                    } else {
                        mockContent = { question: `(AI) ${sourceSummary} sorusu #${i + 1}?`, point: points };
                    }
                    generatedQuestions.push(mockContent);
                }

                const existingGroup = this.aiPoolGroups.find(g => g.type === req.type && g.difficulty === req.difficulty);
                if (existingGroup) {
                    existingGroup.count += parseInt(req.count);
                    existingGroup.questions = existingGroup.questions.concat(generatedQuestions);
                } else {
                    this.aiPoolGroups.push({
                        id: Date.now() + Math.random(),
                        type: req.type,
                        typeName: typeName,
                        difficulty: req.difficulty,
                        difficultyLabel: difficultyLabel,
                        count: parseInt(req.count),
                        questions: generatedQuestions
                    });
                }
            });
            this.aiLoading = false;
            this.aiBatchModalOpen = false;
        }, 1500);
    },


    openAiModal(item) { this.activeAiItem = item; this.aiModalOpen = true; this.aiPrompt = ''; this.aiContext = ''; this.aiFile = null; },
    generateAiContent() {
        if (!this.activeAiItem) return;
        this.aiLoading = true;
        setTimeout(() => {
            const sourceSummary = (this.aiPrompt ? this.aiPrompt : '') + (this.aiContext ? ' + Metin' : '') + (this.aiFile ? ' + Dosya' : '');
            const type = this.activeAiItem.type;
            let newContent = {};
            if (type === 'multiple_choice') newContent = { question: `(AI) ${sourceSummary} sorusu?`, point: '15', options: ['A', 'B', 'C', 'D'] };
            else newContent = { question: `(AI) ${sourceSummary} sorusu?`, point: '10' };
            if (Object.keys(newContent).length > 0) this.activeAiItem.content = newContent;
            this.aiLoading = false;
            this.aiModalOpen = false;
        }, 1000);
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
    select(id) { if (this.cursorMode === 'select') this.selectedId = id; },
    deselect() { this.selectedId = null; },
    remove(id) { this.elements = this.elements.filter(el => el.id !== id); this.selectedId = null; },

    async uploadImage(event, item) {
        const file = event.target.files[0];
        if (!file) return;

        // Kullanıcıya işlem yapıldığını gösterelim
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
            // İşlem bitince input'u sıfırla (aynı resmi tekrar seçebilmek için)
            event.target.value = '';

            // Loading'i kapat
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
        }
    },

    async saveExam() {

        if (!confirm('Sınav kağıdını kaydetmek istediğine emin misin?')) return;
        const title = this.examTitle;

        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));

        const url = this.examId ? `/exam/update/${this.examId}` : '/exam/save';

        try {
            const response = await axios.post('/exam/save',
                {
                    title: title,
                    elements: this.elements,
                    page_count: this.totalPages
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.token
                    }
                }
            );

            if (response.data.success) {
                setTimeout(() => {
                    window.location.href = "/library";
                }, 1000);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Sınav başarıyla kaydedildi! 💾', type: 'success' }
                }));
            }

        } catch (error) {
            console.error("Kayıt Hatası:", error);

            let errorMsg = 'Kaydetme sırasında bir hata oluştu!';
            if (error.response && error.response.status === 419) {
                errorMsg = 'Oturum süreniz dolmuş, lütfen sayfayı yenileyin.';
            }

            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: errorMsg, type: 'error' }
            }));
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
        } finally {
            this.aiLoading = false;
        }
    },
    saveToConsole() { console.log(JSON.stringify(this.elements)); window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Kayıt Başarılı!', type: 'success' } }));; }
}));

Alpine.start();