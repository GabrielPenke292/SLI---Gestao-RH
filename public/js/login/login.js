document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    const btnText = btn.querySelector('.btn-text');
    const loading = btn.querySelector('.loading');
    
    btnText.style.display = 'none';
    loading.style.display = 'inline-block';
    btn.disabled = true;
});

// Adicionar efeito de foco nos inputs e controlar labels
document.querySelectorAll('.form-control').forEach(input => {
    // Verificar se o input já tem valor (para manter o label no lugar correto)
    if (input.value) {
        input.parentElement.classList.add('focused');
    }
    
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
    
    input.addEventListener('input', function() {
        if (this.value) {
            this.parentElement.classList.add('focused');
        } else {
            this.parentElement.classList.remove('focused');
        }
    });
});