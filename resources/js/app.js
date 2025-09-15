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




Alpine.data("quizPlayer", () => ({
    active: 0,
    answers: {}, // [questionId]: answerId   1(index)-8 , 

    init() {

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
            window.location.href = result.data.redirect;
        }
        catch (error) {
            console.log(error)
        }
    }


}));





Alpine.start();