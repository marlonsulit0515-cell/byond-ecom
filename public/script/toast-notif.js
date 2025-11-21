window.showToast = function(message, duration = 2500) {
    const toast = document.getElementById('toast');
    const msg = document.getElementById('toast-message');

    if (!toast || !msg) return;

    msg.innerText = message;
    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.display = 'none';
    }, duration);
};