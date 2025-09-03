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

          // === Iconos de alérgenos solo si existen ===
          let allergensIcons = "";
          if (Array.isArray(item.allergensImg) && item.allergensImg.length > 0) {
            allergensIcons = `
              <div class="d-flex gap-2 flex-wrap mt-2">
                ${item.allergensImg.map(icon => `
                  <img src="${icon}" class="img-fluid rounded-circle" 
                       style="width:50px; height:50px;" alt="Alergeno de ${item.name}">
                `).join("")}
              </div>
            `;
          } else if (typeof item.allergensImg === "string" && item.allergensImg.trim() !== "") {
            allergensIcons = `
              <div class="d-flex gap-2 mt-2">
                <img src="${item.allergensImg}" class="img-fluid rounded-circle" 
                     style="width:50px; height:50px;" alt="Alergeno de ${item.name}">
              </div>
            `;
          }

          // === Imagen del producto solo si existe ===
          let productImage = "";
          if (item.img && item.img.trim() !== "") {
            productImage = `<img src="${item.img}" class="card-img-top mb-2" alt="${item.name}">`;
          } else {
            // placeholder invisible para mantener alturas
            productImage = `<div class="mb-2" style="height:150px"></div>`;
          }

          // === Alerta en formato badge compacto ===
          let alertBadge = "";
          if (item.alert && item.alert.trim() !== "") {
            alertBadge = `<span class="badge bg-info d-inline-block px-2 py-1 mb-2">${item.alert}</span>`;
          } else {
            alertBadge = `<span class="badge bg-light invisible px-2 py-1 mb-2">placeholder</span>`;
          }

          col.innerHTML = `
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">${item.name}</h5>

                <!-- Descripción -->
                <p class="card-text text-info">${item.description || "&nbsp;"}</p>

                <!-- Alerta -->
                ${alertBadge}

                <!-- Imagen del producto -->
                ${productImage}

                <!-- Iconos de alérgenos -->
                ${allergensIcons || ""}

                <!-- Descripción de alérgenos -->
                <p class="card-text text-secondary">${item.allergensDes || "&nbsp;"}</p>

                <!-- Precios -->
                <p class="card-text text-primary fw-bold mt-auto">
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
