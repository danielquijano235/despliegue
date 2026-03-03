// Helper para persistir y recuperar platos del menú en localStorage
const STORAGE_KEY = 'bookit:menu:platos';

export const getPlatos = () => {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return null;
    return JSON.parse(raw);
  } catch (e) {
    console.error('Error leyendo platos desde localStorage', e);
    return null;
  }
};

export const savePlatos = (platos) => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(platos));
  } catch (e) {
    console.error('Error guardando platos en localStorage', e);
  }
};

export default { getPlatos, savePlatos };
