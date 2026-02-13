// main.js
document.addEventListener('DOMContentLoaded', () => {
    // Favorites System
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.target.classList.toggle('active');
            // Simulate favorite storage
            localStorage.setItem(`fav-${Date.now()}`, 'true');
        });
    });

    // Video Carousel
    const videos = document.querySelectorAll('.video-thumb');
    const mainVideo = document.querySelector('.featured-video');
    
    videos.forEach(thumb => {
        thumb.addEventListener('click', () => {
            const videoSrc = thumb.dataset.video;
            mainVideo.src = `media/${videoSrc}`;
            videos.forEach(v => v.classList.remove('active'));
            thumb.classList.add('active');
            mainVideo.load();
            mainVideo.play();
        });
    });

    // Interactive Map Pins
    document.querySelectorAll('.map-pin').forEach(pin => {
        pin.addEventListener('click', () => {
            const region = pin.querySelector('.pin-info').textContent;
            alert(`Showing attractions in ${region}!`);
        });
    });

    // Hover Effects
    document.querySelectorAll('.attraction-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.querySelector('.card-hover-content').style.opacity = '1';
        });
        
        card.addEventListener('mouseleave', () => {
            card.querySelector('.card-hover-content').style.opacity = '0';
        });
    });
});