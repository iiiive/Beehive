<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HiveCare</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Roboto', sans-serif;
      scroll-behavior: smooth; 
    }

    header {
      position: relative;
      background-image: url('images/homepage.jpeg');
      background-size: cover;
      background-position: center;
      height: 80vh;
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    header::after {
      content: "";
      position: absolute;
      top:0; left:0; right:0; bottom:0;
      background: rgba(0,0,0,0.5);
    }

    header .content {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .top-right-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      z-index: 2;
    }

  header h1 {
    font-family: 'Cursive', 'Brush Script MT', sans-serif;
    font-weight: 100;
    font-size: clamp(3rem, 10vw, 12rem); /* scales between 3rem and 8rem depending on screen */

    }

    header img.logo {
      width: 200px;
      height: auto;
      margin-top: 70px;

    }

    header p {
      font-family: 'Roboto', sans-serif; /* clean sans-serif font */
      font-size: 1.5rem;
      margin-bottom: 60px;
    }

    .btn-custom {
      background-color: #ffb300;
      color: white;
      font-weight: bold;
      border-radius: 50px;
      padding: 12px 30px;
            margin-bottom: 80px;

    }

    .btn-custom:hover {
      background-color: #e6a500;
      color: white;
    }

    .info-section {
      padding: 80px 20px;
      background-color: #fffbee;
    }

   .info-card {
 --card-gradient: rgba(0, 0, 0, 0.8);
  --card-blend-mode: overlay;
  background-color: #fff;
  border-radius: 0.5rem;
  box-shadow: 0.1rem 0.1rem 0.3rem -0.03rem rgba(0, 0, 0, 0.45);
  padding-bottom: 1rem;
  background-image: linear-gradient(
    var(--card-gradient),
    white max(9.5rem, 27vh)
  );
  overflow: cover;
}
.info-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
}
.info-card img {
    border-radius: 0.5rem 0.5rem 0 0;
    width: 100%;
    object-fit: cover;
    // height: max(10rem, 25vh);
    max-height: max(10rem, 30vh);
    aspect-ratio: 4/3;
    mix-blend-mode: var(--card-blend-mode);
    // filter: grayscale(100);

    ~ * {
      margin-left: 1rem;
      margin-right: 1rem;
    }
  }


.info-card h3 {
  margin-top: 1rem;
  font-size: 1.25rem;
}

.info-card a {
  color: inherit;
}

.info-card-wrapper {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(30ch, 1fr));
  gap: 1.5rem;
  max-width: 100vw;
  width: 150ch;
  padding-left: 1rem;
  padding-right: 1rem;
}




    .trivia-section {
      padding: 60px 20px;
      text-align: center;
      background-color: #fff3cd;
      border-left: 8px solid #ffb300;
      max-width: 800px;
      margin: 40px auto;
      border-radius: 12px;
      font-size: 1.2rem;
    }
    body{
      background-image: url('images/frontindex.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
    }

    @media(max-width:768px) {
      header h1 {
        font-size: 2.5rem;
      }
      header p {
        font-size: 1.2rem;
      }
      .top-right-btn {
        top: 10px;
        right: 15px;
      }
      header img.logo {
        width: 60px;
      }
    }
    
*,::before,::after{
  margin: 0;
}

@property --angle {
  syntax: "<angle>";
  initial-value: 0deg;
  inherits: true;
}
/* general styling */
html {
	color-scheme: dark light;
}
img{
  max-width: 100%;
}
/* Hide radio buttons */
input[type="radio"] {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

body{

  min-height: 100svh;
  display: grid;
  place-content: center;
  margin: 0;
  padding: 1rem;
  font: 1rem system-ui;
}

.cards{
  --img-w: 200px;
  --duration: 300ms;
  --img-easing: cubic-bezier(0.34, 1.56, 0.64, 1);
  width: min(100% - 4rem, 800px);
  margin-inline: auto;
  display: grid;
  
  counter-reset: my-counter;
}

.card{
  --cards-grid-cols: auto;
  --cards-grid-rows: var(--img-w) auto;
  --cards-grid-gap: 2rem;
  --cards-footer-justify: center;
  
  grid-area: 1/1;
  display: grid;
 place-items: center; 
  grid-template-columns: var(--cards-grid-cols);
  grid-template-rows: var(--cards-grid-rows);
  gap: var(--cards-grid-gap);
  
}

@media (600px < width){
  .card{
      --cards-grid-cols: var(--img-w) auto;
      --cards-grid-rows: auto;
      --cards-grid-gap: 4rem;
      --cards-footer-justify: start;
  }
}


.card-img{
  width: 200px;
  height: 200px;
  aspect-ratio: 1 / 1 ;
  rotate: var(--angle, 0deg);
  border-radius: 10px;
  border: 3px solid #FFF;
  overflow: hidden;
  transform-origin: center;
  object-fit: cover;
  box-shadow: 0 0 5px 3px rgba(0 0 0 / .05);
}



input:nth-of-type(1):checked + .card ~ .card > .card-img{
  animation: straighten-img-1 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
.card:has(~input:nth-of-type(2):checked) > .card-img,
input:nth-of-type(2):checked + .card ~ .card > .card-img{
  animation: straighten-img-2 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
.card:has(~input:nth-of-type(3):checked) > .card-img,
input:nth-of-type(3):checked + .card ~ .card > .card-img{
  animation: straighten-img-3 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
.card:has(~input:nth-of-type(4):checked) > .card-img,
input:nth-of-type(4):checked + .card ~ .card > .card-img{
  animation: straighten-img-4 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
.card:has(~input:nth-of-type(5):checked) > .card-img,
input:nth-of-type(5):checked + .card ~ .card > .card-img{
  animation: straighten-img-5 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
.card:has(~input:nth-of-type(6):checked) > .card-img,
input:nth-of-type(6):checked + .card ~ .card > .card-img{
  animation: straighten-img-6 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
.card:has(~input:nth-of-type(7):checked) > .card-img,
input:nth-of-type(7):checked + .card ~ .card > .card-img{
  animation: straighten-img-7 calc(var(--duration) * 2) forwards;
  animation-timing-function: var(--img-easing);
}
/* as CSS can't remove animations, we change the animation according to which checkbox is checked  - all animations are the same (would be simpler with SCSS) */
@keyframes straighten-img-1 { 50%{ --angle: 0deg;} }
@keyframes straighten-img-2 { 50%{ --angle: 0deg;} }
@keyframes straighten-img-3 { 50%{ --angle: 0deg;} }
@keyframes straighten-img-4 { 50%{ --angle: 0deg;} }
@keyframes straighten-img-5 { 50%{ --angle: 0deg;} }
@keyframes straighten-img-6 { 50%{ --angle: 0deg;} }
@keyframes straighten-img-7 { 50%{ --angle: 0deg;} }


/* stacking order - these are updated according to which card is selected */
.card{
  z-index: -1;
}
input:checked + .card{
  z-index:10 !important;
}
/* next card checked - place behind */
.card:has(+input:checked){
  z-index:9;
}
/* next card +1 checked - place behind */
.card:has(+input + .card + input:checked){
  z-index:8;
}
/* next card +2 checked - place behind */
.card:has(+input + .card +input + .card + input:checked){
  z-index:7;
}
/* next card +3 checked - place behind */
.card:has(+input + .card +input + .card +input + .card + input:checked){
  z-index:6;
}
/* next card +4 checked - place behind */
.card:has(+input + .card +input + .card +input + .card +input + .card + input:checked){
  z-index:5;
}
/* next card +5 checked - place behind */
.card:has(+input + .card +input + .card +input +input + .card +input + .card +input + .card + input:checked){
  z-index:4;
}
/* next card +6 checked - place behind */
.card:has(+input + .card +input + .card +input  + .card +input +input + .card +input + .card +input + .card + input:checked){
  z-index:3;
}

.card-data{
  display: grid;
  gap: 1rem;
}
.card-data > .card-num{
  opacity: var(--data-opacity, 0);
  font-size: .8rem;
  color: #666;
}
.card-data > p{
  font-size: 0.9rem;

}
.card-data > h2,
.card-data > p{
  transition: var(--duration) ease-in-out;
  transition-delay: var(--data-delay,0ms);
  opacity: var(--data-opacity, 0);
  translate: 0 var(--data-y, 20px);
}
.card-data > footer{
  display: flex;
  justify-content: var(--cards-footer-justify);
  gap: 2rem;
}
.card-data > footer label{
  margin-block-start: auto;
  cursor: pointer;
  pointer-events: var(--card-events, none);
  opacity: var(--data-opacity, 0);
  transition: color var(--duration) ease-in-out;
  color: var(--label-clr-txt,#000);
  background-color:var(--label-clr-bg,#EEE);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  aspect-ratio: 1/1;
  display: grid;
  place-content: center;
  transition: background-color 300ms ease-in-out,color  300ms ease-in-out;
}


input:checked:focus-visible + .card > .card-data > footer label,
.card-data > footer label:hover{
  --label-clr-txt: #FFF;
  --label-clr-bg: steelblue;
}

input:checked + .card{
  --data-opacity: 1;
  --data-y: 0;
  --data-delay: var(--duration);
  --card-events: auto;
  transition: z-index;
  transition-delay: 300ms;
  /*z-index: 1;*/
}

input:checked +.card > .card-img{
  animation: reveal-img calc(var(--duration) * 2) forwards;
}

@keyframes reveal-img{
  50%{
    translate: -150% 0;
    --angle: 0deg;
  }
}

.our-team {
  padding: 30px 0 40px;
  margin-bottom: 30px;
  background-color: #f7f5ec;
  text-align: center;
  overflow: hidden;
  position: relative;
}

.our-team .picture {
  display: inline-block;
  height: 130px;
  width: 130px;
  margin-bottom: 50px;
  z-index: 1;
  position: relative;
}

.our-team .picture::before {
  content: "";
  width: 100%;
  height: 0;
  border-radius: 50%;
  background-color: #1369ce;
  position: absolute;
  bottom: 135%;
  right: 0;
  left: 0;
  opacity: 0.9;
  transform: scale(3);
  transition: all 0.3s linear 0s;
}

.our-team:hover .picture::before {
  height: 100%;
}

.our-team .picture::after {
  content: "";
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: #1369ce;
  position: absolute;
  top: 0;
  left: 0;
  z-index: -1;
}

.our-team .picture img {
  width: 100%;
  height: auto;
  border-radius: 50%;
  transform: scale(1);
  transition: all 0.9s ease 0s;
}

.our-team:hover .picture img {
  box-shadow: 0 0 0 14px #f7f5ec;
  transform: scale(0.7);
}

.our-team .title {
  display: block;
  font-size: 15px;
  color: #4e5052;
  text-transform: capitalize;
}

.our-team .social {
  width: 100%;
  padding: 0;
  margin: 0;
  background-color: #1369ce;
  position: absolute;
  bottom: -100px;
  left: 0;
  transition: all 0.5s ease 0s;
}





  </style>
</head>
<body>

  <header>
    <a href="homepage.php" class="btn btn-custom top-right-btn">Get Started</a>

    <div class="content">
        
      <h1>HiveCare</h1>
      <p>Learn about stingless bees in the Philippines!</p>
      <a href="#info" class="btn btn-custom btn-lg">Read More</a>
    </div>
  </header>

  <section id="info" class="info-section">
    <div class="container">
      <h2 class="text-center mb-5">STINGLESS BEES FACTS</h2>
  <ul class="info-card-wrapper">
  <li class="info-card">
    <img src="https://www.goodnet.org/photos/620x0/37405_hd.jpg" alt="Pollination">
    <h3>POLLINATION ROLE</h3>
    <p>Stingless bees (Tetragonula biroi) in the Philippines are promoted for boosting pollination of high-value crops like mango, coconut, and bitter gourd.</p>
  </li>
  <li class="info-card">
    <img src="https://static.vecteezy.com/system/resources/previews/023/701/789/large_2x/honey-on-black-background-illustration-ai-generative-free-photo.jpg" alt="Medicinal Honey">
    <h3>MEDICINAL HONEY</h3>
    <p>Their honey has strong antibacterial and antioxidant properties, effective even against drug-resistant bacteria, making it valuable as a nutraceutical.</p>
  </li>
  <li class="info-card">
    <img src="https://i.pinimg.com/originals/e5/bc/4c/e5bc4cd5aca3ee38eed95305baf45fe6.jpg" alt="Beekeeping">
    <h3>EASY BEEKEEPING</h3>
    <p>Stingless beekeeping is low-maintenance since the bees are native, adaptable to many flowers, and can live in simple hive setups.</p>
  </li>
</ul>



        <div class="col-12 text-center mt-4">
        <h2>DID YOU KNOW?</h2>
    
        <div class="cards">

  <input type="radio" id="radio-1" name="radio-card" checked>
  <article class="card" style="--angle:4deg">
    <img class="card-img" src="https://www.rosepestcontrol.com/wp-content/uploads/2019/07/Honey_bee_hero.jpg">
   <div class="card-data">
      <span class="card-num">1/7</span>
      <h2>Honey Collection</h3>
      <p>Let honey drip naturally from pots overnight instead of pressing, to preserve brood and pot integrity..</p>
      <footer>
        <label for="radio-7" aria-label="Previous">&#10094;</label>
        <label for="radio-2" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>

  <!-- card 2 -->
  <input type="radio" id="radio-2" name="radio-card">
  <article class="card" style="--angle:-8deg">
    <img class="card-img" src="https://picsum.photos/id/30/200/300">
    <div class="card-data">
      <span class="card-num">2/7</span>
      <h2>Colony Transfer</h3>
      <p>Move stingless bee colonies into new hive boxes at night when they are calmer, reducing stress and bee loss.</p>
      <footer>
        <label for="radio-1" aria-label="Previous">&#10094;</label>
        <label for="radio-3" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>

  <!-- card 3 -->
    <input type="radio" id="radio-3" name="radio-card">
  <article class="card" style="--angle:-7deg">
    <img class="card-img" src="https://picsum.photos/id/39/200/300">
    <div class="card-data">
      <span class="card-num">3/7</span>
      <h2>Harvest Timing</h3>
      <p>Avoid harvesting during cloudy or rainy days to protect honey quality and prevent colony disruption.</p>
      <footer>
        <label for="radio-2" aria-label="Previous">&#10094;</label>
        <label for="radio-4" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>

  <!-- card 4 -->
    <input type="radio" id="radio-4" name="radio-card">
  <article class="card" style="--angle:11deg">
    <img class="card-img" src="https://picsum.photos/id/103/200/300">
    <div class="card-data">
      <span class="card-num">4/7</span>
      <h2>Low Maintenance</h3>
      <p>Unlike honey bees (Apis), Tetragonula biroi require fewer parasite checks, making them ideal for small beekeepers.</p>
        <footer>
        <label for="radio-3" aria-label="Previous">&#10094;</label>
        <label for="radio-5" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>

  <!-- card 5 -->
    <input type="radio" id="radio-5" name="radio-card" >
  <article class="card" style="--angle:13deg">
    <img class="card-img" src="https://picsum.photos/id/175/200/300">
    <div class="card-data">
      <span class="card-num">5/7</span>
      <h2>Medicinal Value</h3>
      <p>Their honey and propolis show antimicrobial properties, including activity against drug-resistant bacteria.</p>
        <footer>
        <label for="radio-4" aria-label="Previous">&#10094;</label>
        <label for="radio-6" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>

  <!-- card 6 -->
    <input type="radio" id="radio-6" name="radio-card">
  <article class="card" style="--angle:-17deg">
    <img class="card-img" src="https://picsum.photos/id/349/200/300" >
    <div class="card-data">
      <span class="card-num">6/7</span>
      <h2>Honey Characteristics</h3>
      <p>The honey's color and composition vary by region, ranging from extra light amber to dark amber with differing acidity and mineral content.</p>
      <footer>
        <label for="radio-5" aria-label="Previous">&#10094;</label>
        <label for="radio-7" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>

  <!-- card 7 -->
  <input type="radio" id="radio-7" name="radio-card" >
  <article class="card" style="--angle:20deg">
    <img class="card-img"src="https://picsum.photos/id/401/200/300">
    <div class="card-data">
      <span class="card-num">7/7</span>
      <h2>Foraging Flexibility</h3>
      <p>T. biroi accept many flower types, improving honey flavor and ensuring year-round food supply and colony health.</p>
        <footer>
        <label for="radio-6" aria-label="Previous">&#10094;</label>
        <label for="radio-1" aria-label="Next">&#10095;</label>
      </footer>
    </div>
  </article>
  
  </div>

  
      </div>
      <div class="container">
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="our-team">
        <div class="picture">
          <img class="img-fluid" src="https://picsum.photos/130/130?image=1027">
        </div>
       <div class="team-content">
  <h3 class="name">Hans Staden</h3>
  <h4 class="title">German Mercenary/Naturalist</h4>
  <p class="description">
One of the earliest Europeans to record stingless bees and their honey in Brazil. He documented how Amerindians used “pot honey” long before modern beekeeping practices.</div>
       
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="our-team">
        <div class="picture">
          <img class="img-fluid" src="https://picsum.photos/130/130?image=839">
        </div>
               <div class="team-content">
  <h3 class="name">Hans Staden</h3>
  <h4 class="title">German Mercenary/Naturalist</h4>
  <p class="description">
Professor Emeritus at UPLB and a pioneer of stingless bee research in the Philippines. Her studies from 2021–2023 shaped best practices for honey quality and sustainable meliponiculture.  </p>
</div>
       
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="our-team">
        <div class="picture">
          <img class="img-fluid" src="https://picsum.photos/130/130?image=856">
        </div>
               <div class="team-content">
  <h3 class="name">Hans Staden</h3>
  <h4 class="title">German Mercenary/Naturalist</h4>
  <p class="description">
A researcher who studied the biomedical value of Philippine stingless bee honey. In 2021, his work showed its strong antibiotic potential against multidrug-resistant bacteria.  </p>
</div>
        
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="our-team">
        <div class="picture">
          <img class="img-fluid" src="https://picsum.photos/130/130?image=836">
        </div>
               <div class="team-content">
  <h3 class="name">Hans Staden</h3>
  <h4 class="title">German Mercenary/Naturalist</h4>
  <p class="description">
Introduced a compact deep-learning model (Apis-Prime) for automated hive weight monitoring. His innovation improved the accuracy of remote data collection for beekeeping management.  </p>
</div>
        
      </div>
    </div>
  </div>
</div>
    </div>
  </section>


</body>
</html>
