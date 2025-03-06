import { supabase } from "./supabase.js";
import { registerUser } from "./api.js";

// 🔹 Iniciar sesión con Google
export async function loginWithGoogle() {
  try {
    const { data, error } = await supabase.auth.signInWithOAuth({
      provider: "google",
      options: { 
        redirectTo: "http://localhost:3000/sistema_de_cita_odontologica-main/index.php" // Redirige a index.php
      },
    });

    if (error) {
      return;
    }
  } catch (error) {
  }
}

// 🔹 Verificar sesión activa y procesar usuario autenticado
export async function checkUserSession() {
  try {
    const { data, error } = await supabase.auth.getSession();

    if (error) {
      return;
    }

    if (data?.session) {
      const user = data.session.user;

      // Verificar en el backend y redirigir
      await registerUser({
        email: user.email,
        user_metadata: {
          full_name: user.user_metadata.full_name || user.email.split('@')[0]
        }
      });
    } else {
      // Redirigir a index.php si no hay sesión (opcional)
      if (!window.location.pathname.includes('index.php')) {
        window.location.href = '/sistema_de_cita_odontologica-main/index.php';
      }
    }
  } catch (error) {
  }
}

// 🔹 Escuchar cambios en la autenticación
let hasChecked = false; // Control para evitar verificaciones múltiples por evento
supabase.auth.onAuthStateChange(async (event, session) => {
  if (hasChecked) return; // Evitar ejecuciones repetidas en el mismo evento
  if (event === "SIGNED_IN" && session) {
    hasChecked = true;
    await checkUserSession(); // Verificar una sola vez tras SIGNED_IN
  } else if (event === "SIGNED_OUT") {
    hasChecked = false;
    window.location.href = '/sistema_de_cita_odontologica-main/index.php';
  }
});

// 🔹 Ejecutar verificación al cargar cualquier página
document.addEventListener("DOMContentLoaded", () => {
  hasChecked = false; // Resetear hasChecked para cada carga de página
  checkUserSession(); // Verificar en cada página
});