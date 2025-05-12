document.addEventListener('DOMContentLoaded', ()=>{
    const boutons = document.querySelectorAll('.like');
    console.log(boutons);
    boutons.forEach((bouton) => {
        bouton.addEventListener('click', function(event){
            event.preventDefault();

            fetch(this.href)
                .then(response => response.json())
                .then((data) => {
                    console.log("data"+data);
                    this.querySelector('.nbrLikes').innerHTML = data.count;

                    const thumb = this.querySelector('.thumb');
                    if (data.liked) {
                        thumb.classList.remove('bi-hand-thumbs-up');
                        thumb.classList.add('bi-hand-thumbs-up-fill');
                    } else {
                        thumb.classList.remove('bi-hand-thumbs-up-fill');
                        thumb.classList.add('bi-hand-thumbs-up');
                    }
                });
        });
    });
});
