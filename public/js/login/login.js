$(document).ready(function() {
    // Evento de submit do formulário
    $('#loginForm').on('submit', function(e) {
        const $btn = $('#loginBtn');
        const $btnText = $btn.find('.btn-text');
        const $loading = $btn.find('.loading');
        
        $btnText.hide();
        $loading.show();
        $btn.prop('disabled', true);
    });

    // Adicionar efeito de foco nos inputs e controlar labels
    $('.form-control').each(function() {
        const $input = $(this);
        const $parent = $input.parent();
        
        // Verificar se o input já tem valor (para manter o label no lugar correto)
        if ($input.val()) {
            $parent.addClass('focused');
        }
        
        // Evento de foco
        $input.on('focus', function() {
            $(this).parent().addClass('focused');
        });
        
        // Evento de blur
        $input.on('blur', function() {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });
        
        // Evento de input
        $input.on('input', function() {
            if ($(this).val()) {
                $(this).parent().addClass('focused');
            } else {
                $(this).parent().removeClass('focused');
            }
        });
    });
});