// Play/pause video on markerFound or markerLost events
// Show/hide play/pause button
AFRAME.registerComponent('markerhandler', {
    init: function () {
        const video = document.querySelector('video'),
            btnPlayPauseContainer = document.querySelector('.btn-playpause-container');
        if(video) {
            this.el.sceneEl.addEventListener('markerFound', () => {
                video.play();
                if (video.paused || video.ended) {
                    btnPlayPauseContainer.style.display = 'flex';
                } else {
                    btnPlayPauseContainer.style.display = 'none';
                }
            });
            this.el.sceneEl.addEventListener('markerLost', () => {
                video.pause();
            });
            document.querySelector('#btn-play-pause').addEventListener('click', () => { 
                video.play();
            });
        }
    }
});

// camera video stream has been retrieved 
// correctly and appended to the DOM
// Fires after camera-init event
window.addEventListener('arjs-video-loaded', () => {
    document.querySelector('.arjs-loader').style.display = 'none';
});

// Shows play/pause button on video end
window.addEventListener('load', () => {
    const video = document.querySelector('video'),
        btnPlayPauseContainer = document.querySelector('.btn-playpause-container');
    if(video) {
        video.addEventListener('ended', () => {
            btnPlayPauseContainer.style.display = 'flex';
        });
    }
});