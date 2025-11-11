@props(['disabled' => false])

<div class="input-wrapper">
    <input @disabled($disabled) {{ $attributes->merge(['class' => 'material-input']) }}>
    <label>{{ $attributes->get('placeholder') ?? 'Input' }}</label>
    <div class="input-line"></div>
</div>

<style>
    .input-wrapper {
        position: relative;
        margin-bottom: 24px;
    }

    .material-input {
        width: 100%;
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--color-primary-muted);
        border-radius: 0;
        padding: 16px 0 8px;
        color: var(--text-main);
        font-size: 16px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        transition: all 0.2s ease;
        outline: none;
        position: relative;
        z-index: 3;
    }

    .material-input::placeholder {
        color: transparent;
    }

    .material-input:focus {
        border-bottom-color: var(--color-primary);
    }

    .input-wrapper label {
        position: absolute;
        left: 0;
        top: 16px;
        color: var(--text-subtle);
        font-size: 16px;
        font-weight: 400;
        transition: all 0.2s ease;
        pointer-events: none;
        transform-origin: left top;
        z-index: 4;
    }

    .material-input:focus + label,
    .material-input:not(:placeholder-shown) + label {
        transform: translateY(-24px) scale(0.75);
        color: var(--color-primary);
        font-weight: 500;
    }

    .input-line {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--color-primary);
        transition: width 0.3s ease;
        z-index: 5;
    }

    .material-input:focus ~ .input-line {
        width: 100%;
    }

    .material-input:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.material-input').forEach(input => {
            // Add subtle scale effect on focus
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    });
</script>