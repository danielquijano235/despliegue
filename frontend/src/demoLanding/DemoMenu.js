import React from 'react';
import { Link, useNavigate } from 'react-router-dom';

const categories = [
  { id: 'para-empezar', title: 'PARA EMPEZAR', image: '/assets/images/para-empezar.jpg' },
  { id: 'sopas', title: 'SOPAS', image: '/assets/images/sopas.jpg' },
  { id: 'carnes', title: 'CARNES', image: '/assets/images/carnes.jpg' },
  { id: 'parrilla', title: 'PARRILLA', image: '/assets/images/parrilla.jpg' },
  { id: 'especiales', title: 'ESPECIALES', image: '/assets/images/especiales.jpg' },
  { id: 'ensaladas', title: 'ENSALADAS', image: '/assets/images/ensaladas.jpg' },
  { id: 'hamburguesas', title: 'HAMBURGUESAS', image: '/assets/images/hamburguesas.jpg' },
  { id: 'infantil', title: 'INFANTIL', image: '/assets/images/infantil.jpg' },
  { id: 'cafes', title: 'CAFÉS', image: '/assets/images/cafes.jpg' },
  { id: 'jugos', title: 'JUGOS', image: '/assets/images/jugos.jpg' },
  { id: 'bebidas', title: 'BEBIDAS', image: '/assets/images/bebidas.jpg' },
  { id: 'licores', title: 'LICORES', image: '/assets/images/licores.jpg' },
];

const DemoMenu = () => {
  const navigate = useNavigate();

  const handleVerMenu = (e) => {
    e.preventDefault();
    // ensure viewport is at the top before navigating so the menu opens from the top
    window.scrollTo({ top: 0, behavior: 'auto' });
    navigate('/menu');
  };

  return (
    <section className="demo-menu demo-menu-single" id="menu">
      <div className="demo-menu-inner">
        <h2>Menú</h2>
        <p>Descubre nuestro menú por categorías</p>

        <div className="demo-menu-cta">
          <a href="/menu" onClick={handleVerMenu} className="demo-menu-btn">Ver menú</a>
        </div>
      </div>
    </section>
  );
};

export default DemoMenu;
