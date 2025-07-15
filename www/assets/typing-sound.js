function createTypingSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    
    function createSingleKeySound() {
        const oscillator = audioContext.createOscillator();
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(Math.random() * 100 + 400, audioContext.currentTime);
        
        const gainNode = audioContext.createGain();
        gainNode.gain.setValueAtTime(0.05, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.1);
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.1);
    }
    
    return {
        playKeySound: function() {
            createSingleKeySound();
        }
    };
}