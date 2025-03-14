import { supabase } from "./supabase.js";
import { registerUser } from "./api.js";

// Iniciar sesión con Google
export async function loginWithGoogle() {
    try {
        const { data, error } = await supabase.auth.signInWithOAuth({
            provider: "google",
            options: { 
                redirectTo: "http://localhost:3000/sistema_de_cita_odontologica-main/index.php" // Redirige a index.php tras autenticación
            },
        });

        if (error) return;
    } catch (error) {}
}

// Verificar sesión activa y procesar usuario autenticado
export async function checkUserSession() {
    try {
        const { data, error } = await supabase.auth.getSession();
        if (error) return;

        if (data?.session) {
            const user = data.session.user;
            // Registrar usuario en el backend
            await registerUser({
                email: user.email,
                user_metadata: {
                    full_name: user.user_metadata.full_name || user.email.split('@')[0]
                }
            });
        } else if (!window.location.pathname.includes('index.php')) {
            // Redirigir a index.php si no hay sesión activa
            window.location.href = '/sistema_de_cita_odontologica-main/index.php';
        }
    } catch (error) {}
}

// Escuchar cambios en la autenticación
let hasChecked = false; // Control para evitar verificaciones múltiples por evento
supabase.auth.onAuthStateChange(async (event, session) => {
    if (hasChecked) return;
    if (event === "SIGNED_IN" && session) {
        hasChecked = true;
        await checkUserSession();
    } else if (event === "SIGNED_OUT") {
        hasChecked = false;
        window.location.href = '/sistema_de_cita_odontologica-main/index.php';
    }
});

// Verificar sesión al cargar cualquier página
document.addEventListener("DOMContentLoaded", () => {
    hasChecked = false; // Resetear control para cada carga de página
    checkUserSession();
});