document.addEventListener("DOMContentLoaded", () => {
  fetch("data/menu.json")
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById("menu-container");
      const filtros = document.getElementById("menu-filtros");

      // Crear botones de categoría
      Object.keys(data).forEach((categoria, index) => {
        const btn = document.createElement("button");
        btn.className = `btn btn-outline-primary ${index === 0 ? "active" : ""}`;
        btn.textContent = categoria;
        btn.onclick = () => mostrarCategoria(categoria, data);
        filtros.appendChild(btn);
      });

      // Mostrar primera categoría por defecto
      mostrarCategoria(Object.keys(data)[0], data);

      function mostrarCategoria(categoria, data) {
        container.innerHTML = "";

        data[categoria].forEach(item => {
          const col = document.createElement("div");
          col.className = "col-md-4";

          // Generar los iconos de alérgenos (si hay varios)
          let allergensIcons = "";
          if (Array.isArray(item.allergensImg)) {
            allergensIcons = `
              <div class="d-flex gap-2 flex-wrap mt-2">
                ${item.allergensImg.map(icon => `
                  <img src="${icon}" class="img-fluid rounded-circle" 
                       style="width:40px; height:40px;" alt="Alergeno de ${item.name}">
                `).join("")}
              </div>
            `;
          } else if (item.allergensImg) {
            // Soporte para un solo icono (string en vez de array)
            allergensIcons = `
              <div class="d-flex gap-2 mt-2">
                <img src="${item.allergensImg}" class="img-fluid rounded-circle" 
                     style="width:40px; height:40px;" alt="Alergeno de ${item.name}">
              </div>
            `;
          }

        col.innerHTML = `
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">${item.name}</h5>
              <p class="card-text text-info">${item.description || ""}</p>
              ${item.alert && item.alert.trim() !== "" 
                ? `<p class="btn btn-info">${item.alert}</p>` 
                : ""}
              <img src="${item.img}" class="card-img-top mb-2" alt="${item.name}">
              ${allergensIcons}
              <p class="card-text text-secondary">${item.allergensDes || ""}</p>
              <p class="card-text text-primary fw-bold">
                ${item.price1 || ""} ${item.price2 || ""} ${item.price3 || ""}
              </p>
            </div>
          </div>
        `;

          container.appendChild(col);
        });

        Array.from(filtros.children).forEach(btn => {
          btn.classList.remove("active");
          if (btn.textContent === categoria) btn.classList.add("active");
        });
      }
    })
    .catch(error => {
      console.error("Error cargando el menú:", error);
      document.getElementById("menu-container").innerHTML =
        `<p class="text-danger">No se pudo cargar el menú. Revisa el archivo menu.json.</p>`;
    });
});
