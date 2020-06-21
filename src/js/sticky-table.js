export default function () {
  const table = document.querySelector(".research-table");
  if (!table) return;

  const checkPos = () => {
    const topPos = table.getBoundingClientRect().top;
    if (topPos < 100) {
      stickTable();
    } else {
      stickTable(false);
    }
  };

  const stickTable = (isStick = true) => {
    const mobileResolution = 990;
    const top = window.innerWidth < mobileResolution ? 32 : 146;
    const topWithAdmin = document.querySelector('#wpadminbar') ? top : top - 32;
    const thead = table.querySelector("thead");
    thead.style.position = isStick ? "fixed" : "static";
    thead.style.top = isStick ? `${topWithAdmin}px` : '';
    const thTitle = table.querySelector("tbody tr td:first-child");
    const thWidth = thTitle && thTitle.offsetWidth;
    const widhts = [thWidth, 110, 125];
    const ths = thead.querySelectorAll("th");
    ths.forEach((th, index) => {
      th.width = isStick ? `${widhts[index]}px` : "auto";
    });
  };

  checkPos();

  window.onscroll = () => {
    checkPos();
  };
}
