<div class="auth-container">
    <div class="auth-card">
        <h1>Restablecer Contraseña</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($tokenValido) && $tokenValido): ?>
            <p class="auth-description">Ingresa tu nueva contraseña.</p>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Mínimo 6 caracteres"
                        required
                        minlength="6"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password2">Repetir contraseña</label>
                    <input 
                        type="password" 
                        id="password2" 
                        name="password2" 
                        placeholder="Repite tu contraseña"
                        required
                        minlength="6"
                    >
                </div>
                
                <button type="submit" class="btn-primary">Restablecer contraseña</button>
            </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <a href="<?= BASE_URL ?>login" class="link-secondary">Volver al login</a>
        </div>
    </div>
</div>