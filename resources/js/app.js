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

//? Quiz Player
Alpine.data("quizPlayer", (props) => ({
    active: 0,
    answers: {},
    durationMinutes: props.durationMinutes,
    startTime: props.startTime,

    checkUrl: props.checkUrl,
    token: props.token,

    countdownText: "Time: --:--",
    intervalId: null,

    init() {

        if (props.isNew) {
            localStorage.removeItem('quiz-state');
        }


        const storedState = JSON.parse(localStorage.getItem("quiz-state"));


        if (storedState) {
            this.durationMinutes = parseInt(storedState.durationMinutes);
            this.startTime = parseInt(storedState.startTime);
            this.active = storedState.active || 0;
            this.answers = storedState.answers || {};
        }

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

    startTimer() {
        const totalDurationInSeconds = this.durationMinutes * 60;

        this.intervalId = setInterval(() => {
            const currentTimeInSeconds = Math.floor(Date.now() / 1000);
            const elapsedTimeInSeconds = currentTimeInSeconds - this.startTime;
            const timeLeftInSeconds = totalDurationInSeconds - elapsedTimeInSeconds;

            if (timeLeftInSeconds <= 0) {
                this.countdownText = "Süre Doldu!";
                clearInterval(this.intervalId);
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Süre Doldu!', type: 'warning' } }));
                this.submitQuiz();

                return;
            }

            const minutes = Math.floor(timeLeftInSeconds / 60);
            const seconds = timeLeftInSeconds % 60;
            this.countdownText = `Time: ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        }, 1000);
    },

    toggle(qid, aid) {
        if (this.answers[qid] == aid) {
            delete this.answers[qid];
        } else {
            this.answers[qid] = aid;
        }
    },

    prev() {
        if (this.active > 0) this.active--;
    },

    next(total) {
        if (this.active < total) this.active++;
    },

    async submitQuiz() {
        console.log("Sınav gönderiliyor...", this.answers);

        try {
            const result = await axios.post(this.checkUrl, this.answers, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.token,
                },
                withCredentials: true
            });

            console.log(result.data);
            localStorage.removeItem("quiz-state");
            window.location.href = result.data.redirect;
        } catch (error) {
            console.error("Sınav gönderilirken hata oluştu:", error);
            // Opsiyonel: Toast mesajı vs.
        }
    }
}));

//? Quiz Learning Player
Alpine.data("learningPlayer", (props) => ({
    active: 0,
    totalQuestions: props.total,
    quizId: props.id,
    checkUrl: props.checkUrl,     // Backend URL'si eklendi
    token: props.token,           // CSRF Token eklendi
    storageKey: 'quiz_learning_state_' + props.id,

    firstAnswers: {},
    openedExplanations: [],

    init() {
        const savedState = localStorage.getItem(this.storageKey);
        if (savedState) {
            try {
                const parsed = JSON.parse(savedState);
                this.active = parsed.active !== undefined ? parsed.active : 0;
                this.firstAnswers = parsed.firstAnswers || {};
                this.openedExplanations = parsed.openedExplanations || [];
            } catch (e) {
                console.error("Kayıtlı durum okunamadı:", e);
            }
        }

        this.$watch('active', () => {
            this.saveState();
        });
    },

    saveState() {
        const state = {
            active: this.active,
            firstAnswers: this.firstAnswers,
            openedExplanations: this.openedExplanations
        };
        localStorage.setItem(this.storageKey, JSON.stringify(state));
    },

    // DİKKAT: qId (Soru ID) parametresi eklendi
    selectAnswer(qIndex, qId, aId, isCorrect, correctOptionId) {
        let key = qIndex + '_' + aId;

        // İLK DEFA CEVAP VERİLİYORSA:
        if (!this.firstAnswers[qIndex]) {
            // Soru ID'sini de (qId) kaydediyoruz ki Backend'e yollayabilelim
            this.firstAnswers[qIndex] = { qId: qId, aId: aId, correct: isCorrect };

            if (!this.openedExplanations.includes(key)) {
                this.openedExplanations.push(key);
            }

            if (!isCorrect) {
                let correctKey = qIndex + '_' + correctOptionId;
                if (!this.openedExplanations.includes(correctKey)) {
                    this.openedExplanations.push(correctKey);
                }
            }
        }
        else {
            if (this.openedExplanations.includes(key)) {
                this.openedExplanations = this.openedExplanations.filter(k => k !== key);
            } else {
                this.openedExplanations.push(key);
            }
        }

        this.saveState();
    },

    isExplanationOpen(qIndex, aId) { return this.openedExplanations.includes(qIndex + '_' + aId); },

    getOptionClass(qIndex, aId, isCorrect) {
        let first = this.firstAnswers[qIndex];
        if (!first) return 'bg-[#1e1e1e] border-gray-700 hover:border-gray-500 text-gray-200';
        if (first.aId === aId) { // id yerine aId oldu
            return isCorrect ? 'bg-emerald-900/20 border-emerald-500 text-emerald-400' : 'bg-rose-900/20 border-rose-500 text-rose-400';
        }
        if (isCorrect) return 'bg-emerald-900/10 border-emerald-500/50 text-emerald-400';
        if (this.isExplanationOpen(qIndex, aId)) return 'bg-[#252526] border-indigo-500/30 text-gray-300';
        return 'bg-[#161616] border-[#2d2d2d] text-gray-500 opacity-60 hover:opacity-100';
    },

    getIconClass(qIndex, aId, isCorrect) {
        let first = this.firstAnswers[qIndex];
        if (!first) return 'bg-gray-800 border-gray-600 text-gray-400 group-hover:text-white';
        if (first.aId === aId) { // id yerine aId oldu
            return isCorrect ? 'bg-emerald-500 text-white border-emerald-400' : 'bg-rose-500 text-white border-rose-400';
        }
        if (isCorrect) return 'text-emerald-400 border-emerald-500/50 bg-gray-800';
        if (this.isExplanationOpen(qIndex, aId)) return 'text-indigo-400 border-indigo-500/30 bg-gray-800';
        return 'bg-gray-800 border-gray-700 text-gray-600';
    },

    showStatusIcon(qIndex, aId, isCorrect) {
        let first = this.firstAnswers[qIndex];
        if (!first) return false;
        return (first.aId === aId) || isCorrect; // id yerine aId oldu
    },

    getSidebarClass(index) {
        if (this.active === index) return 'border-indigo-500 bg-indigo-500 text-white shadow-md scale-105 z-10';
        if (this.firstAnswers[index]) {
            return this.firstAnswers[index].correct
                ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-400'
                : 'border-rose-500/50 bg-rose-500/10 text-rose-400';
        }
        return 'border-gray-700 bg-gray-800 text-gray-500 hover:border-gray-500 hover:text-gray-300';
    },

    next() { if (this.active < this.totalQuestions - 1) this.active++; window.scrollTo({ top: 0 }); },
    prev() { if (this.active > 0) this.active--; window.scrollTo({ top: 0 }); },
    jump(index) { this.active = index; window.scrollTo({ top: 0 }); },

    // YENİ: Backend'e POST atma mekanizması
    async finish() {
        if (!confirm('Çalışmayı bitirip sonuçları görmek istediğine emin misin?')) return;

        // 1. Veriyi Backend'in beklediği formata çevir: { soru_id: cevap_id, soru_id2: cevap_id2 }
        let payload = {};
        for (let key in this.firstAnswers) {
            let answerData = this.firstAnswers[key];
            payload[answerData.qId] = answerData.aId;
        }

        try {
            // Axios kullandıysan axios.post yapabilirsin, veya native fetch:
            const response = await axios.post(this.checkUrl, payload, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.token,
                },
                withCredentials: true
            });

            // Başarılıysa localStorage'ı temizle ve sonuç sayfasına (redirect url) git
            localStorage.removeItem(this.storageKey);
            window.location.href = response.data.redirect;

        } catch (error) {
            console.error("Sonuçlar kaydedilirken hata:", error);
            alert("Sonuçlar kaydedilemedi. Lütfen bağlantını kontrol et.");
        }
    }
}));



//? Quiz Create
Alpine.data("quizCreate", (props = {}) => ({
    token: props.token || '',
    negativeMarkingEnabled: props.negativeMarkingEnabled || false, // Props'tan gelmeli
    fileName: "",
    fileUrl: "",
    errors: props.errors || {},

    sourceFileName: null,
    sourceFile: null,
    aiLoading: false,

    // --- KATEGORİ YÖNETİMİ ---
    allCategories: props.allCategories || [],
    selectedCategories: props.selectedCategories || [], // DÜZELTİLDİ: props'tan alıyoruz
    categorySearch: '',

    showTitleModal: false,
    tempTitle: '', // Modal içindeki input için
    targetUrlToSubmit: null,

    // --- FONKSİYONLAR ---
    toggleCategory(id) {
        if (this.selectedCategories.includes(id)) {
            // Varsa çıkar
            this.selectedCategories = this.selectedCategories.filter(c => c !== id);
        } else {
            // Yoksa ekle
            this.selectedCategories.push(id);
        }
    },

    get filteredCategories() {
        if (this.categorySearch === '') {
            return this.allCategories;
        }
        const search = this.categorySearch.toLocaleLowerCase('tr-TR');
        return this.allCategories.filter(cat =>
            cat.name.toLocaleLowerCase('tr-TR').includes(search)
        );
    },

    getCategoryName(id) {
        const cat = this.allCategories.find(c => c.id == id);
        return cat ? cat.name : 'Bilinmeyen';
    },

    setFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            this.fileUrl = URL.createObjectURL(file);
        }
    },

    setSourceFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.sourceFileName = file.name;
            this.sourceFile = file;
        }
    },

    hasError(field) {
        return this.errors && this.errors[field];
    },

    getError(field) {
        return this.errors[field][0];
    },

    async submitQuiz(url = null) {

        let formElement = document.getElementById('quiz-create-form');
        let currentTitle = document.getElementById('title').value;

        // 1. KONTROL: Eğer isim hala "Yeni Quiz" ise (veya boşsa) modalı aç ve durdur.
        if (currentTitle.trim() === 'Yeni Quiz' || currentTitle.trim() === '') {
            this.tempTitle = currentTitle.trim() === 'Yeni Quiz' ? '' : currentTitle; // Modalda boş gelsin
            this.targetUrlToSubmit = url; // Kullanıcı kaydet diyince bu url'yi kullanacağız
            this.showTitleModal = true;

            // Kullanıcıyı uyar (Opsiyonel)
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen quiz için bir isim belirleyin.', type: 'warning' } }));
            return;
        }

        // Eğer isim değiştirilmişse, normal kaydetme işlemine devam et
        this.processSubmission(url);
    },

    saveTitleAndContinue() {
        if (this.tempTitle.trim() === '') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Quiz adı boş olamaz!', type: 'error' } }));
            return;
        }

        // 1. Ana formdaki title inputunu güncelle
        document.getElementById('title').value = this.tempTitle;

        // 2. Modalı kapat
        this.showTitleModal = false;

        // 3. Gerçek kaydetme işlemini başlat
        this.processSubmission(this.targetUrlToSubmit);
    },

    async processSubmission(url) {
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'İşlem yapılıyor, lütfen bekleyin...', type: 'info' } }));

        let formElement = document.getElementById('quiz-create-form');
        let targetUrl = url ? url : formElement.action;

        let formData = new FormData(formElement);

        if (this.sourceFile) {
            formData.append('source_file', this.sourceFile);
        }

        if (!this.negativeMarkingEnabled) {
            formData.delete('wrong_to_correct_ratio');
            formData.append('wrong_to_correct_ratio', 0);
        }

        try {
            const response = await axios.post(targetUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': this.token
                }
            });

            if (response.data.success) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: response.data.message, type: 'success' } }));
                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 1000);
            }

        } catch (error) {
            console.error(error);
            if (error.response && error.response.status === 422) {
                this.errors = error.response.data.errors;
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen hatalı alanları kontrol edin.', type: 'error' } }));
            } else {
                let msg = error.response?.data?.message || 'Bir hata oluştu.';
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
            }
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
        }
    }
}));

//? Question Create
Alpine.data("questionCreate", (props = {}) => ({
    quizId: props.quizId,
    total_questions: props.number_of_questions ?? 0,
    total_options: props.number_of_options ?? 2,
    current_q_index: 0,
    errors: [],
    sourceFileName: null,
    aiLoading: false,
    questions: [], // Başlangıçta boş, init'te dolduracağız

    // --- BAŞLATMA (INIT) FONKSİYONU ---
    init() {
        // Eğer veritabanından gelen sorular varsa (Edit Modu)
        if (props.existingQuestions && props.existingQuestions.length > 0) {
            this.questions = props.existingQuestions.map(q => {

                // 1. Mevcut cevapları formatla
                let formattedAnswers = q.answers.map(a => ({
                    answer_content: a.answer_text,
                    is_correct: Boolean(a.is_correct)
                }));

                // 2. EKSİK CEVAPLARI TAMAMLA (Hata Çözümü Burası)
                // Eğer olması gereken seçenek sayısı (this.total_options), DB'den gelenden fazlaysa boş ekle
                if (formattedAnswers.length < this.total_options) {
                    const diff = this.total_options - formattedAnswers.length;
                    for (let k = 0; k < diff; k++) {
                        formattedAnswers.push({
                            answer_content: "",
                            is_correct: false
                        });
                    }
                }
                // (Opsiyonel) Eğer DB'de fazlası varsa kırp (nadiren gerekir ama güvenli olur)
                else if (formattedAnswers.length > this.total_options) {
                    formattedAnswers = formattedAnswers.slice(0, this.total_options);
                }

                return {
                    id: q.id,
                    title: q.title,
                    content: q.question_text,
                    point: q.points,
                    img_url: null,
                    fileUrl: q.img_url ? `/storage/${q.img_url}` : null,
                    fileName: q.img_url ? 'Mevcut Resim' : '',
                    answers: formattedAnswers // Güncellenmiş cevap dizisi
                };
            });

            // Eğer soru sayısı artırıldıysa ve DB'dekiler azsa, kalanı boş soru ile doldur
            if (this.questions.length < this.total_questions) {
                const diff = this.total_questions - this.questions.length;
                for (let i = 0; i < diff; i++) {
                    this.questions.push(this.createEmptyQuestion());
                }
            }
            else if (this.questions.length > this.total_questions) {
                // Örneğin DB'de 5 soru var ama ayarda 4 seçildi. İlk 4 tanesini al, 5.'yi at.
                this.questions = this.questions.slice(0, this.total_questions);
            }
        }
        else {
            // Yeni Oluşturma Modu (Hepsi boş)
            this.questions = Array.from({ length: this.total_questions }, () => this.createEmptyQuestion());
        }
    },

    // Boş soru şablonu oluşturan yardımcı fonksiyon
    createEmptyQuestion() {
        return {
            title: null,
            content: "",
            point: 1,
            img_url: null,
            fileName: "",
            fileUrl: "",
            answers: Array.from({ length: this.total_options }, () => ({
                answer_content: "",
                is_correct: false,
            })),
        };
    },

    // ... (nextQuestion, prevQuestion, goToQuestion, setFile fonksiyonların AYNEN KALSIN) ...
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
        return this.errors[errorKey] && this.errors[errorKey].length > 0;
    },
    getError(field) {
        const errorKey = `questions.${this.current_q_index}.${field}`;
        if (this.hasError(field)) {
            // Mesajı temizleme (opsiyonel)
            return this.errors[errorKey][0].replace(`questions.${this.current_q_index}.`, '').replace(' field', '');
        }
        return '';
    },

    // ... (submitForm ve generateSingleQuestionAI fonksiyonların AYNEN KALSIN) ...
    async submitForm(checkUrl, token) {
        // ... Senin mevcut kodların ...
        // Sadece küçük bir not: Edit modunda eski soruları güncellemek için 
        // Backend tarafında logic kurman gerekebilir (örn: eski soruları silip yenileri eklemek gibi)
        let formData = new FormData();
        formData.append("quizId", this.quizId);

        this.questions.forEach((q, idx) => {
            formData.append(`questions[${idx}][content]`, q.content || '');
            formData.append(`questions[${idx}][points]`, q.point);
            if (q.id) {
                formData.append(`questions[${idx}][id]`, q.id);
            }
            if (q.img_url instanceof File) { // Sadece yeni dosya varsa gönder
                formData.append(`questions[${idx}][img_url]`, q.img_url);
            }
            if (q.title) {
                formData.append(`questions[${idx}][title]`, q.title);
            }
            q.answers.forEach((a, a_idx) => {
                formData.append(`questions[${idx}][answers][${a_idx}][answer_content]`, a.answer_content || '');
                const isCorrectValue = a.is_correct ? 1 : 0;
                formData.append(`questions[${idx}][answers][${a_idx}][is_correct]`, isCorrectValue)
            })
        });

        try {
            const result = await axios.post(checkUrl, formData, {
                headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': token },
            });
            if (result.data.redirect) window.location.href = result.data.redirect;
        } catch (error) {
            console.error(error);
            if (error.response && error.response.status === 422) {
                this.errors = error.response.data.errors;
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen alanları kontrol edin.', type: 'error' } }));
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
    tempCategories: props.initialCategories || [],
    tempDescription: props.initialDescription || '',
    initialCategories: props.initialCategories || [], // Blade'den gelen (Config değil props kullanıyoruz)
    initialDescription: props.initialDescription || '',
    allCategories: props.allCategories || [],
    categorySearch: '',

    documentType: props.documentType || 'exam',

    get currentPageElements() {
        if (!Array.isArray(this.elements)) return [];
        return this.elements.filter(el => el.page === this.activePage);
    },

    get selectedItem() {
        return this.elements.find(el => el.id === this.selectedId);
    },

    get filteredCategories() {
        if (this.categorySearch === '') {
            return this.allCategories;
        }
        const search = this.categorySearch.toLocaleLowerCase('tr-TR');
        return this.allCategories.filter(cat =>
            cat.name.toLocaleLowerCase('tr-TR').includes(search)
        );
    },

    // Toggle Fonksiyonu (Eskisiyle aynı kalabilir ama garanti olsun diye buraya da yazıyorum)
    toggleCategory(id) {
        const targetId = Number(id); // Kesinlikle sayıya çeviriyoruz

        // Mevcut dizideki her şeyi de sayıya çeviriyoruz ki eşitlik bozulmasın
        const currentCats = this.tempCategories.map(c => Number(c));

        if (currentCats.includes(targetId)) {
            // Varsa, filtrele ve YENİ bir dizi ata (Alpine.js bunu anında algılar)
            this.tempCategories = currentCats.filter(c => c !== targetId);
        } else {
            // Yoksa, mevcut dizinin sonuna ekleyip YENİ dizi ata
            this.tempCategories = [...currentCats, targetId];
        }
    },


    init() {

        this.elements = this.elements.filter(el => el.id && el.page);
        this.tempTitle = this.examTitle;
        this.tempDescription = this.initialDescription;
        // Gelen kategori objelerini [1, 3] gibi ID listesine çevir
        if (Array.isArray(this.initialCategories)) {
            this.tempCategories = this.initialCategories.map(c => c.id);
        }

        if (window.QUIZ_TO_IMPORT) {
            this.importQuizFromData(window.QUIZ_TO_IMPORT);
            window.QUIZ_TO_IMPORT = null;
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
    // YENİ: Sınavı Dizgiye Sokan Sihirli Fonksiyon
    // Güncellenmiş Sihirli Fonksiyon
    importQuizFromData(quizData) {
        this.examTitle = quizData.title + " (Baskı Formatı)";
        this.tempTitle = this.examTitle;
        this.tempDescription = quizData.description || '';

        if (quizData.categories && Array.isArray(quizData.categories)) {
            this.tempCategories = quizData.categories.map(c => c.id);
        }

        // Quiz'deki soruları bizim formatımıza çevirip sayfaya (dağınık da olsa) atıyoruz
        this.elements = [];
        if (quizData.questions) {
            quizData.questions.forEach((q, index) => {
                let optionsList = q.answers ? q.answers.map(a => a.answer_text) : [];

                // --- CEVAP ANAHTARI İÇİN EKSİK OLAN KISIM BURASI ---
                let correctAnswerLetter = '';
                if (q.answers) {
                    // Veritabanında doğru cevabı tutan kolonun "is_correct" olduğunu varsayıyorum.
                    // (Eğer senin veritabanında "isCorrect" veya "correct" ise burayı ona göre değiştir)
                    let correctIndex = q.answers.findIndex(a => a.is_correct == 1 || a.is_correct === true);

                    if (correctIndex !== -1) {
                        const letters = ['A', 'B', 'C', 'D', 'E'];
                        correctAnswerLetter = letters[correctIndex] || ''; // 0. index A, 1. index B olur...
                    }
                }
                // ----------------------------------------------------

                this.elements.push({
                    id: Date.now() + Math.random(),
                    page: 1, type: 'multiple_choice',
                    content: {
                        number: `${index + 1}.`,
                        question: q.question_text,
                        point: q.points || '10',
                        options: optionsList,
                        answer_key: correctAnswerLetter // İŞTE ŞİMDİ EKLENDİ!
                    },
                    x: 0, y: 0, w: 700, h: 100, // Geçici değerler, autoLayout bunu dizecek
                    styles: { fontSize: 14, fontWeight: 'normal', textAlign: 'left', color: '#000', borderWidth: 0, backgroundColor: 'transparent' }
                });
            });
        }

        // Quiz'i aldık, şimdi JİLET GİBİ diz! (Varsayılan normal dizilim)
        this.autoLayout('twocolumn');

        // Otomatik dizgi işlemi bittikten hemen sonra Cevap Anahtarını da otomatik oluştur!
        setTimeout(() => {
            this.generateAnswerKey();
        }, 300);
    },
    // YENİ: METNİN GERÇEK YÜKSEKLİĞİNİ KUSURSUZ ÖLÇEN FONKSİYON
    calculateExactHeight(el, targetWidth) {
        let ruler = document.createElement('div');
        ruler.style.position = 'absolute';
        ruler.style.visibility = 'hidden'; // Ekranda görünmeyecek
        ruler.style.width = targetWidth + 'px';
        ruler.style.boxSizing = 'border-box';

        // Elemanın font ve padding ayarlarını birebir uygula
        ruler.style.fontSize = (el.styles?.fontSize || 14) + 'px';
        ruler.style.fontWeight = el.styles?.fontWeight || 'normal';
        ruler.style.fontFamily = el.styles?.fontFamily || 'Roboto, sans-serif';
        ruler.style.padding = (el.styles?.padding || 0) + 'px';

        // Taşırma ve satır atlama kuralları PDF ile aynı olmalı
        ruler.style.whiteSpace = 'pre-wrap';
        ruler.style.wordWrap = 'break-word';
        ruler.style.lineHeight = '1.4';

        // İçeriği ekle (Enter'ları <br>'ye çevirerek HTML'in anlamasını sağla)
        let text = el.content || '';
        ruler.innerHTML = text.replace(/\n/g, '<br>');

        // Gizlice sayfaya ekle, boyunu ölç ve geri sil
        document.body.appendChild(ruler);
        let exactHeight = ruler.getBoundingClientRect().height;
        document.body.removeChild(ruler);

        // Güvenlik marjı (Alt boşluk) ekleyerek döndür
        let bottomMargin = (el.type === 'heading') ? 25 : 15;
        return exactHeight + bottomMargin;
    },
    // AUTO Layout
    autoLayout(layoutType = 'normal') {
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));

        // TÜM İŞLEMLERİ BU 150ms GECİKMENİN İÇİNE ALIYORUZ!
        setTimeout(() => {

            let userElements = this.elements.filter(el => el.type !== 'header_block' && el.type !== 'student_info');

            userElements.sort((a, b) => {
                if (a.page !== b.page) return a.page - b.page;
                return a.y - b.y;
            });

            this.elements = [];
            this.activePage = 1;
            this.totalPages = 1;

            let isStudyGuide = (this.documentType === 'study_guide');

            const PAGE_WIDTH = 794; const PAGE_HEIGHT = 1123; const MARGIN_Y = 60;
            let isCompact = layoutType === 'compact' || layoutType === 'twocolumn';
            let isTwoCol = layoutType === 'twocolumn';

            let fontSize = isCompact ? 11 : 14;
            let optSpacing = isCompact ? 18 : 25;
            let itemSpacing = isCompact ? 8 : 15;

            let itemWidth = isTwoCol ? 330 : 700;
            let MARGIN_X = isTwoCol ? 50 : 47;
            let COL_2_X = 414;

            let currentY = MARGIN_Y;
            let currentPage = 1;
            let currentColumn = 1;

            this.elements.push({
                id: Date.now() + Math.random(), page: currentPage, type: 'header_block',
                content: {
                    title: this.examTitle.toUpperCase(),
                    faculty: 'Kurum / Okul Adı',
                    term: isStudyGuide ? 'Ders Notu & Özet' : 'Sınav Kağıdı'
                },
                x: (PAGE_WIDTH - 600) / 2, y: currentY, w: 600, h: 80,
                styles: { fontSize: 14, fontWeight: 'bold', textAlign: 'center', color: '#000', borderWidth: 0, backgroundColor: 'transparent' }
            });
            currentY += 90;

            if (!isStudyGuide) {
                this.elements.push({
                    id: Date.now() + Math.random(), page: currentPage, type: 'student_info',
                    content: { label1: 'Adı Soyadı:', val1: '', label2: 'Numara:', val2: '', label3: 'Sınıfı:', val3: '', label4: 'Puan:', val4: '' },
                    x: 47, y: currentY, w: 700, h: isCompact ? 60 : 80,
                    styles: { fontSize: fontSize, fontWeight: 'normal', textAlign: 'left', color: '#000', borderWidth: 0, backgroundColor: 'transparent' }
                });
                currentY += isCompact ? 70 : 100;
            }

            let currentX = MARGIN_X;
            let columnStartY = currentY;
            let questionCounter = 1;

            if (userElements.length > 0) {
                const questionTypes = ['multiple_choice', 'open_ended', 'fill_in_blanks', 'true_false'];
                const textTypes = ['text', 'heading', 'sub_heading', 'box', 'code']; // YENİ: Code buraya eklendi ki cetvel ile ölçülsün

                userElements.forEach((el) => {

                    let estimatedHeight = 0;

                    if (questionTypes.includes(el.type)) {
                        el.content.number = `${questionCounter}.`;
                        questionCounter++;

                        let qText = el.content.question || '';
                        let optionsList = el.content.options || [];

                        let charsPerLine = isTwoCol ? 42 : 95;
                        if (isCompact) charsPerLine += 15;
                        let lineHeight = isCompact ? 16 : 22;

                        let qLines = Math.ceil((qText.length || 1) / charsPerLine);
                        estimatedHeight = (qLines * lineHeight) + 15;

                        if (el.type === 'multiple_choice') {
                            optionsList.forEach(opt => {
                                let optText = opt || '';
                                let optLines = Math.ceil((optText.length + 5) / charsPerLine) || 1;
                                estimatedHeight += (optLines * lineHeight) + 4;
                            });
                            estimatedHeight += 10;
                        }
                        else if (el.type === 'open_ended') {
                            estimatedHeight += isCompact ? 70 : 100;
                        }
                        else if (el.type === 'fill_in_blanks' || el.type === 'true_false') {
                            estimatedHeight += 20;
                        }

                        el.styles.fontSize = fontSize;
                        el.w = itemWidth;
                    }
                    else if (textTypes.includes(el.type)) {
                        if (el.type === 'heading') {
                            el.styles.fontSize = 22; el.styles.fontWeight = 'bold';
                        } else if (el.type === 'sub_heading') {
                            el.styles.fontSize = 16; el.styles.fontWeight = 'bold';
                        } else if (el.type !== 'code') {
                            el.styles.fontSize = fontSize;
                        }

                        el.w = itemWidth;
                        estimatedHeight = this.calculateExactHeight(el, itemWidth);
                    }
                    else {
                        estimatedHeight = parseFloat(el.h) || 50;
                        if (parseFloat(el.w) > itemWidth) {
                            el.w = itemWidth;
                        }
                    }

                    let currentItemSpacing = isTwoCol ? itemSpacing + 10 : itemSpacing;

                    if (currentY + estimatedHeight > PAGE_HEIGHT - MARGIN_Y) {
                        if (isTwoCol && currentColumn === 1) {
                            currentColumn = 2; currentX = COL_2_X; currentY = columnStartY;
                        } else {
                            currentPage++; this.totalPages = currentPage; currentColumn = 1;
                            currentX = MARGIN_X; currentY = MARGIN_Y; columnStartY = MARGIN_Y;
                        }
                    }

                    el.page = currentPage;
                    el.x = currentX;
                    el.y = currentY;
                    el.h = estimatedHeight;

                    this.elements.push(el);
                    currentY += estimatedHeight + currentItemSpacing;
                });
            }

            // İŞLEMLER BİTİNCE LOADING KAPANIR
            this.activePage = 1;
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Belge otomatik hizalandı!', type: 'success' } }));

        }, 150); // BÜTÜN DİZGİ KODU BU SÜSLÜ PARANTEZİN İÇİNDE KALMALI
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

    generateAnswerKey() {
        // 1. Sadece soru olanları ve içinde cevap (answer_key) olanları filtrele
        const questionTypes = ['multiple_choice', 'open_ended', 'true_false', 'fill_in_blanks'];
        let questions = this.elements.filter(el => questionTypes.includes(el.type) && el.content && el.content.answer_key);

        if (questions.length === 0) {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Cevap anahtarı oluşturulacak soru bulunamadı.', type: 'warning' } }));
            return;
        }

        // 2. Soruları sayfadaki dizilişlerine (Y koordinatına) göre sırala
        questions.sort((a, b) => {
            if (a.page !== b.page) return a.page - b.page;
            return a.y - b.y;
        });

        // 3. En sona yepyeni bir sayfa ekle
        this.addPage();
        let targetPage = this.totalPages;
        const PAGE_WIDTH = 794;
        const MARGIN_X = 47; // Sınav kağıdının sol boşluk standardı

        // 4. "CEVAP ANAHTARI" Başlığını ekle (Tam ortaya hizalı)
        this.addItem('heading', PAGE_WIDTH / 2, 100);
        let header = this.elements[this.elements.length - 1];
        header.content = 'CEVAP ANAHTARI';
        header.page = targetPage;
        header.styles.textAlign = 'center';
        header.styles.color = '#d32f2f'; // Öğretmen kırmızısı
        header.y = 80; // Biraz daha yukarı aldık

        // 5. Her soruyu ve cevabını alt alta diz
        let currentY = 160;
        questions.forEach(q => {
            let qNum = q.content.number || '';
            let qText = q.content.question || '';
            let ans = q.content.answer_key;

            this.addItem('text', PAGE_WIDTH / 2, currentY);
            let textItem = this.elements[this.elements.length - 1];

            // METİN İÇERİĞİ: Soru + Alt Satıra Cevap
            textItem.content = `${qNum} ${qText}\n\nCevap: ${ans}`;

            // TASARIM VE HİZALAMA DÜZELTMELERİ
            textItem.page = targetPage;
            textItem.w = 700; // Genişliği tam kağıt boyu yaptık
            textItem.x = MARGIN_X; // Sola sıfırladık (Taşmayı önler!)
            textItem.y = currentY; // Y eksenini kesinleştirdik
            textItem.styles.fontSize = 14; // Yazıları büyüttük (12'den 14'e)
            textItem.styles.fontWeight = 'bold'; // Daha belirgin yaptık
            textItem.styles.textAlign = 'left'; // Sola hizaladık

            // Öğenin tam yüksekliğini hesapla (ki yazılar birbirine girmesin)
            let exactHeight = this.calculateExactHeight(textItem, 700);
            textItem.h = exactHeight;

            // Bir sonraki soru için boşluk ekle (Araları daha açık ve ferah olsun)
            currentY += exactHeight + 25;

            // Eğer sayfa taştıysa (Örn: 1020px'i geçtiyse) yeni sayfaya geç
            if (currentY > 1020) {
                this.addPage();
                targetPage = this.totalPages;
                currentY = 100; // Yeni sayfanın başından devam et
            }
        });

        // 6. Focus'u direkt cevap anahtarı sayfasına gönder ve bildirim ver
        this.activePage = targetPage;
        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Cevap anahtarı başarıyla eklendi!', type: 'success' } }));
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

                    const questionsWithAnswers = group.questions.map(q => {
                        return {
                            ...q,
                            answer_key: q.answer || '' // Yapay zeka answer döndürürse onu answer_key olarak kaydet
                        };
                    });


                    // Havuza Ekle (Artık temizlenmiş 'group.questions' ekleniyor)
                    this.aiPoolGroups.push({
                        id: Date.now() + Math.random(),
                        type: group.type,
                        typeName: typeLabel,
                        difficulty: group.difficulty,
                        difficultyLabel: group.difficulty.toUpperCase(),
                        count: group.questions.length,
                        questions: questionsWithAnswers // <-- Buraya temizlenmiş hali gidiyor
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
                // --- CEVAP ANAHTARI (ANSWER KEY) GÜNCELLEMESİ ---
                if (generatedData.answer) {
                    item.content.answer_key = generatedData.answer;
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

    activeTab: props.activeTab || 'quizzes',
    showPreviewModal: false,
    previewUrl: null,
    iframeLoading: false,


    setTab(tabName) {
        this.activeTab = tabName;
    },

    // Select değiştiğinde formu otomatik göndermek için
    submitFilters() {
        this.$nextTick(() => {
            if (this.$refs.filterForm) {
                this.$refs.filterForm.submit();
            }
        });
    },

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

//? Study Guide (Özet) Create
Alpine.data("studyGuideCreate", (props = {}) => ({
    token: props.token || '',
    fileName: null,
    sourceFile: null,
    isLoading: false,

    allCategories: props.allCategories || [],
    selectedCategories: props.selectedCategories || [],
    categorySearch: '',

    toggleCategory(id) {
        if (this.selectedCategories.includes(id)) {
            this.selectedCategories = this.selectedCategories.filter(c => c !== id);
        } else {
            this.selectedCategories.push(id);
        }
    },

    get filteredCategories() {
        if (this.categorySearch === '') {
            return this.allCategories;
        }
        const search = this.categorySearch.toLocaleLowerCase('tr-TR');
        return this.allCategories.filter(cat =>
            cat.name.toLocaleLowerCase('tr-TR').includes(search)
        );
    },

    getCategoryName(id) {
        const cat = this.allCategories.find(c => c.id == id);
        return cat ? cat.name : 'Bilinmeyen';
    },

    // Dosya seçildiğinde çalışır
    setFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            this.sourceFile = file;
        } else {
            this.fileName = null;
            this.sourceFile = null;
        }
    },

    // Form submit olduğunda çalışır
    async submitGuide() {
        let formElement = document.getElementById('study-guide-form');
        let textContent = document.querySelector('textarea[name="text_content"]').value;

        // 1. Validasyon (En az biri dolu olmalı)
        if (!this.sourceFile && textContent.trim() === '') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lütfen bir PDF yükleyin veya metin yapıştırın!', type: 'warning' } }));
            return;
        }

        // 2. İşlemi Başlat
        this.isLoading = true;
        window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Yapay zeka dokümanı analiz ediyor, lütfen bekleyin...', type: 'info' } }));

        let formData = new FormData(formElement);
        if (this.sourceFile) {
            formData.append('document', this.sourceFile);
        }

        try {
            // 3. İsteği Gönder (Axios)
            const response = await axios.post('/study-guide/generate', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': this.token
                }
            });

            // 4. Başarılı Sonuç
            if (response.data.success) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: response.data.message, type: 'success' } }));

                // Editöre Yönlendir
                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 1000);
            }

        } catch (error) {
            // 5. Hata Yönetimi
            console.error(error);
            let msg = 'İşlem sırasında bir hata oluştu.';

            if (error.response && error.response.status === 422) {
                msg = 'Lütfen form verilerini kontrol edin.';
            } else if (error.response && error.response.data && error.response.data.message) {
                msg = error.response.data.message;
            }

            window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: 'error' } }));
        } finally {
            this.isLoading = false;
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
        }
    }
}));

Alpine.start();