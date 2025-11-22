<div class="auth-container">
    <div class="auth-card">
        <h1>Recuperar Contraseña</h1>
        <p class="auth-description">Ingresa tu email y te enviaremos instrucciones para restablecer tu contraseña.</p>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                ✅ <?= $success ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($success)): ?>
        <form method="POST" action="<?= BASE_URL ?>recovery" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="tu@email.com"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
            </div>
            
            <button type="submit" class="btn-primary">Enviar instrucciones</button>
        </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <a href="<?= BASE_URL ?>login" class="link-secondary">Volver al login</a>
        </div>
    </div>
</div>