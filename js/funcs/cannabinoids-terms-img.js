export function termImageUpload() {
  if (typeof wp === "undefined" || !wp.media) return; // защита для фронта

  const uploadBtn = document.querySelector(".cannabinoid-image-upload");
  const removeBtn = document.querySelector(".cannabinoid-image-remove");
  const inputField = document.querySelector("#cannabinoid-image-id");
  const preview = document.querySelector("#cannabinoid-image-preview");

  if (!uploadBtn || !inputField || !preview) return;

  let frame;

  uploadBtn.addEventListener("click", function (e) {
    e.preventDefault();

    if (frame) {
      frame.open();
      return;
    }

    frame = wp.media({
      title: "Выберите изображение",
      button: { text: "Использовать это изображение" },
      multiple: false,
    });

    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      inputField.value = attachment.id;
      preview.innerHTML = `<img src="${attachment.url}" style="max-width:150px;height:auto;">`;
      if (removeBtn) removeBtn.style.display = "inline-block";
    });

    frame.open();
  });

  if (removeBtn) {
    removeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      inputField.value = "";
      preview.innerHTML = "";
      removeBtn.style.display = "none";
    });
  }
}
