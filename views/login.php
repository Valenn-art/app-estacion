<div class="auth-container">
    <div class="auth-card">
        <h1>Iniciar Sesión</h1>
        
        <?php if (isset($_GET['activated'])): ?>
            <div class="alert alert-success">
                ✅ Cuenta activada exitosamente. Ya puedes iniciar sesión.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['reset'])): ?>
            <div class="alert alert-success">
                ✅ Contraseña restablecida exitosamente. Inicia sesión con tu nueva contraseña.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>login" class="auth-form">
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
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
            </div>
            
            <button type="submit" class="btn-primary">Acceder</button>
        </form>
        
        <div class="auth-links">
            <a href="<?= BASE_URL ?>recovery" class="link-secondary">¿Olvidaste tu contraseña?</a>
            <p class="separator">¿No tienes una cuenta? <a href="<?= BASE_URL ?>register">Registrarse</a></p>
        </div>
    </div>
</div>