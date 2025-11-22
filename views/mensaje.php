<div class="auth-container">
    <div class="auth-card">
        <div class="alert alert-<?= $tipo ?? 'info' ?>">
            <?php if ($tipo === 'error'): ?>
                ⚠️
            <?php elseif ($tipo === 'success'): ?>
                ✅
            <?php else: ?>
                ℹ️
            <?php endif; ?>
            
            <?= $mensaje ?? 'Sin mensaje' ?>
        </div>
        
        <div class="auth-links" style="margin-top: 30px;">
            <a href="<?= BASE_URL ?>login" class="btn-secondary">Ir al Login</a>
        </div>
    </div>
</div>