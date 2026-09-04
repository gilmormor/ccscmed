{{-- Recuadro del rango de fechas de la constancia.
     Compartido por reportrechon y reportrechongen para que no se separen. --}}
<style>
.caja-constancia {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 15px 12px;
    margin: 4px 0 10px;
}

/* Bootstrap 3 pone la leyenda a 100% de ancho, con borde inferior y 21px.
   Aquí se necesita un título discreto pegado al borde del recuadro. */
.caja-constancia-titulo {
    width: auto;
    border: 0;
    margin-bottom: 8px;
    padding: 0 8px;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    line-height: 1.4;
}
.caja-constancia-titulo .fa { color: #999; margin-right: 4px; }

.caja-constancia label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #777;
    margin-bottom: 4px;
}

@media (max-width: 767px) {
    .caja-constancia .row > div + div { margin-top: 8px; }
}
</style>
