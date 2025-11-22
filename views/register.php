<div class="auth-container">
    <div class="auth-card">
        <h1>Crear Cuenta</h1>
        
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
        <form method="POST" action="<?= BASE_URL ?>register" class="auth-form">
            <div class="form-group">
                <label for="nombres">Nombre completo</label>
                <input 
                    type="text" 
                    id="nombres" 
                    name="nombres" 
                    placeholder="Juan Pérez"
                    required
                    value="<?= htmlspecialchars($_POST['nombres'] ?? '') ?>"
                >
            </div>
            
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
            
            <button type="submit" class="btn-primary">Crear Cuenta</button>
        </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <p class="separator">¿Ya tienes cuenta? <a href="<?= BASE_URL ?>login">Iniciar sesión</a></p>
        </div>
    </div>
</div>