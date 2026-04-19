function openModal(id)  { 
    document.getElementById(id).classList.add('open'); 
}

function closeModal(id) { 
    document.getElementById(id).classList.remove('open'); 
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { 
        if(e.target === o) o.classList.remove('open'); 
    });
});

function updateClock() {
    const n = new Date();
    document.getElementById('clock').textContent =
        n.toLocaleDateString('en-MY',{day:'2-digit',month:'short',year:'numeric'}) + '  ' +
        n.toLocaleTimeString('en-MY',{hour:'2-digit',minute:'2-digit'});
}

updateClock(); 
setInterval(updateClock, 1000);

// Highlight active sidebar link
const p = new URLSearchParams(location.search).get('page') || 'members';

document.querySelectorAll('.nav-link').forEach(l => {
    l.classList.remove('active');
    if(l.href.includes('page='+p)) l.classList.add('active');
});