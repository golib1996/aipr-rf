export default function () {
  const wrapper =
    document.querySelector(".research-table") ||
    document.querySelector(".reserach-popup-btn");
  if (!wrapper) {
    console.error("research-wrapper not found");
    return;
  }

  const form = document.querySelector(".research-form");
  // console.log(form);

  let title = null;
  wrapper.onclick = ({ target }) => {
    if (target.tagName !== "BUTTON") {
      console.log("not btn click");
      return;
    }

    title = target.parentElement.parentElement.querySelector(".research-title");
    if (title && title.innerText) {
      title = title.innerText;
    } else {
      title = document.querySelector('a[data-title]');
      title = title ? title.dataset.title : null;
    }

  };
  
  if (!form) {
    console.log("form not founc");
    return;
  }

  const setStatus = (message) => {
    const div = document.createElement("div");
    const messageWrap = form.appendChild(div);
    messageWrap.innerText = message;
    setTimeout(() => {
      messageWrap.remove();
    }, 3000);
  };

  form.onsubmit = (e) => {
    e.preventDefault();
    const data = {};
    const inputs = form.querySelectorAll("input");
    if (!inputs) return;
    inputs.forEach((input) => {
      if (input.name === "name" || input.name === "phone" || input.name === 'email') {
        data[input.name] = input.value;
      }
    });
    data.title = title || "Не удалось определить название иследования";
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (status) {
      if (this.readyState == 4 && this.status == 200) {
        setStatus("Ваша заявка направлена. В ближайшее время с Вами свяжется наш специалист, демо-версия будет выслана на указанный адрес");
      } else {
      }
    };
    xhttp.open("POST", "/build/Ajax.php", true);
    xhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhttp.send(JSON.stringify(data));
  };
}
