import React, { useState, useEffect } from 'react';
import ModalItem from '../componentes/Compartidos/ModalItem';
import { getPlatos } from '../servicios/menuStorage';

const defaultItems = [
  { nombre: 'Entrecot', descripcion: 'Corte premium a la parrilla', precio: 15000, imagen: '/assets/images/carnes.jpg', categoria: 'principales' },
  { nombre: 'Pollo Asado', descripcion: 'Marinado y dorado al horno', precio: 11000, imagen: '/assets/images/para-empezar.jpg', categoria: 'principales' },
  { nombre: 'Costillas BBQ', descripcion: 'Salsa casera y cocción lenta', precio: 13500, imagen: '/assets/images/parrilla.jpg', categoria: 'principales' },
];

const MenuCarnes = () => {
  const [items, setItems] = useState([]);
  const [open, setOpen] = useState(false);
  const [selected, setSelected] = useState(null);

  useEffect(() => {
    const stored = getPlatos();
    if (stored && Array.isArray(stored)) {
      // filtrar por categoría 'principales' (carnes)
      setItems(stored.filter(p => p.categoria === 'principales' || p.categoria === 'carnes'));
    } else {
      setItems(defaultItems);
    }
  }, []);

  const abrir = (it) => { setSelected(it); setOpen(true); };
  const cerrar = () => { setOpen(false); setSelected(null); };

  const formatPrice = (n) => n == null ? '' : ('$' + Number(n).toLocaleString('es-CO'));

  return (
    <main style={{ padding: 24 }}>
      <h1>Carnes</h1>
      <p>Cortes y preparaciones seleccionadas</p>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(220px,1fr))', gap: 16, marginTop: 18 }}>
        {items.map((it) => (
          <article key={it.id || it.nombre} onClick={() => abrir(it)} style={{ padding: 16, borderRadius: 10, background: '#fff', boxShadow: '0 2px 6px rgba(0,0,0,0.08)', cursor: 'pointer' }}>
            <h3 style={{ margin: '0 0 8px' }}>{it.nombre || it.title}</h3>
            <p style={{ margin: '0 0 12px', color: '#555' }}>{it.descripcion || it.desc}</p>
            <div style={{ fontWeight: 700 }}>{formatPrice(it.precio)}</div>
          </article>
        ))}
      </div>

      <ModalItem open={open} item={selected} onClose={cerrar} />
    </main>
  );
};

export default MenuCarnes;
