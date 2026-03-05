/**
 * ============================================
 * BOOKIT - Componente TopVisitas
 * Archivo: componentes/Dashboard/TopVisitas.js
 * ============================================
 *
 * Muestra una lista de los clientes con más visitas.
 */
import React from "react";

const TopVisitas = ({ clientes }) => {
  const lista = (clientes || [])
    .slice()
    .sort((a, b) => (b.visitas || 0) - (a.visitas || 0))
    .slice(0, 5);

  return (
    <div className="top-clientes-card">
      <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
        {lista.length === 0 && (
          <li style={{ padding: "16px", color: "#718096" }}>No hay datos</li>
        )}
        {lista.map((c, idx) => (
          <li
            key={c.nombre + idx}
            style={{
              display: "flex",
              justifyContent: "space-between",
              padding: "10px 0",
              borderBottom: "1px solid #f1f5f9",
            }}
          >
            <div style={{ display: "flex", gap: "12px", alignItems: "center" }}>
              <div
                style={{
                  width: 36,
                  height: 36,
                  borderRadius: 18,
                  backgroundColor: "#E6F2FF",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  color: "#0B69D6",
                  fontWeight: 700,
                }}
              >
                {c.nombre ? c.nombre.charAt(0).toUpperCase() : "?"}
              </div>
              <div>
                <div style={{ fontWeight: 600 }}>{c.nombre}</div>
                <div style={{ fontSize: "0.85rem", color: "#718096" }}>
                  {c.email || ""}
                </div>
              </div>
            </div>
            <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
              <span
                style={{
                  backgroundColor: "#F1F5F9",
                  padding: "6px 10px",
                  borderRadius: 999,
                }}
              >
                {c.visitas}
              </span>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default TopVisitas;
