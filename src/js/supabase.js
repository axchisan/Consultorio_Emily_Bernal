import { createClient } from "https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm";

// credenciales de Supabase
const SUPABASE_URL = "#privada";
const SUPABASE_ANON_KEY = "#oculta";

export const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
