import axios from 'axios';
import Alpine from "alpinejs"
import './bootstrap';
import persist from '@alpinejs/persist';

//Alpinejs
Alpine.plugin(persist)

window.Alpine = Alpine


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



Alpine.start();