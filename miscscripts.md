<!-- js modal -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
  .firstheader {
      text-align: center;
      font-size: 2.0em;
      letter-spacing: 1px;
      padding: 40px;
      color: white;
      font-family: 'Oswald', sans-serif;
  }

  .gallery-image {
    padding: 150px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    background-color: yellowgreen;
  }
  
  .gallery-image img {
    height: 250px;
    width: 350px;
    transform: scale(1.0);
    transition: transform 0.4s ease;
  }
  p{
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
       Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  }
  .img-box {
    box-sizing: content-box;
    margin: 10px;
    height: 250px;
    width: 350px;
    overflow: hidden;
    display: inline-block;
    color: white;
    position: relative;
    background-color: white;
  }
  
  .caption {
    position: absolute;
    bottom: 5px;
    left: 20px;
    opacity: 0.0;
    transition: transform 0.3s ease, opacity 0.3s ease;
  }
  
  .transparent-box {
    height: 250px;
    width: 350px;
    background-color:rgba(0, 0, 0, 0);
    position: absolute;
    top: 0;
    left: 0;
    transition: background-color 0.3s ease;
  }
  
  .img-box:hover img { 
    transform: scale(1.1);
  }
  
  .img-box:hover .transparent-box {
    background-color:rgba(0, 0, 0, 0.5);
  }
  
  .img-box:hover .caption {
    transform: translateY(-20px);
    opacity: 1.0;
  }
  
  .img-box:hover {
    cursor: pointer;
  }
  
  .caption > p:nth-child(2) {
    font-size: 0.8em;
  }
  
  .opacity-low {
    opacity: 0.5;
  }
  /* End of Gallery Images */
  /* Below is Modal of Gallery */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 150px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.9);
    /* Black w/ opacity */
  }
  .modal-content {
    margin: auto;
    display: block;
    width: 600px;
    height: 400px;
    max-width: 700px;
  }
 .fade-in {
	-webkit-animation: fade-in 1.2s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
	        animation: fade-in 1.2s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
}
 @-webkit-keyframes fade-in {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
  @keyframes fade-in {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
  .fade-out {
	-webkit-animation: fade-out 1s ease-out both;
	        animation: fade-out 1s ease-out both;
}
 @-webkit-keyframes fade-out {
    0% {
      opacity: 1;
    }
    100% {
      opacity: 0;
    }
  }
  @keyframes fade-out {
    0% {
      opacity: 1;
    }
    100% {
      opacity: 0;
    }
  }
  
  
  .animated {
    -webkit-animation-duration: 1s;
    animation-duration: 1s;
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
    color:red;
  }
  .right{
    position: absolute;
    left: 72%;
    top: 36%;
    cursor: pointer;
    background-color:yellowgreen;
    font-size: 37px;
    transition: 0.2s;
    padding: 12px;
  }
  .left{
    position: absolute;
    right: 72%;
    top: 36%;
    cursor: pointer;
    font-size: 37px;
    background-color:yellowgreen;
    transition: 0.2s;
    padding: 12px;
  }
  .left:hover{
    transform: scale(1.1)
  }
  .right:hover{
    transform: scale(1.1)
  }
</style>
<div class="gallery-image">
  <div class="img-box"><img src="https://picsum.photos/350/250?image=444" alt=""/></div>
  <div class="img-box"><img src="https://picsum.photos/350/250/?image=232" alt=""/></div>
  <div class="img-box"><img src="https://picsum.photos/350/250/?image=431" alt=""/></div>
  <div class="img-box"><img src="https://picsum.photos/350/250?image=474" alt=""/></div>
  <div class="img-box"><img src="https://picsum.photos/350/250?image=344" alt=""/></div>
  <div class="img-box"><img src="https://picsum.photos/350/250?image=494" alt=""/></div>
</div>
<div class="modal">
  <img class="modal-content" id="img01">
  <i class="large material-icons left">arrow_back</i>
  <i class="large material-icons right">arrow_forward</i>
</div>
<script>
let modal = document.querySelector('.modal');
let slide = document.querySelectorAll('.gallery-image img');
let modalImg = document.getElementById('img01');
let prevBtn = document.querySelector('.left');
let nextBtn = document.querySelector('.right');
i = 0; timer = 0;
window.addEventListener('click', outsideClick);

prevBtn.onclick = function () {
    slide[i].classList.remove('active');
    i--;
    if (i < 0) {
    i = slide.length - 1;
    }
    modalImg.src = slide[i].src;
    slide[i].classList.add('active');
}
nextBtn.onclick = function () {
    slide[i].classList.remove('active');
    i++;
    if(i >= slide.length) {
    i = 0;
    }
    modalImg.src = slide[i].src;
    slide[i].classList.add('active');
}

function outsideClick(e) {
    if(e.target === modal) {
      modal.classList.add('fade-out')
      setTimeout(()=>{
        modal.style.display = 'none';
        modal.classList.remove('fade-out')
      },300)
       
   if (modal.classList.contains('fade-in')) {
       modal.classList.remove('fade-in');
       }
   }
}
for (let i = 0; i < slide.length; i++) {
    let img = slide[i];
    img.onclick = function(e) {
        modal.style.display = 'block';
        modalImg.src = this.src;
        modal.classList.add('fade-in');
    }
} 
</script>


  
