export default () => {
  const btns = document.querySelectorAll(".research-list__btn");

  if (!btns || !btns.length) return;

  btns.forEach((btn) => {
    btn.onclick = function (e) {
      const isClassAdd = btn.classList.toggle('open');
      const subList = btn.parentElement.querySelector("ul");
      // subList.style.height = isClassAdd ? "auto" : "0";
      // subList.style.height = isClassAdd ? subList.offsetHeight + 'px' : '0px';
      const ch = subList.clientHeight,
      sh = subList.scrollHeight,
      isCollapsed = !ch,
      noHeightSet = !subList.style.height;

      subList.style.height = (isCollapsed || noHeightSet ? sh : 0) + "px";
      // subList.classList.toggle('visible');
    };
  });
};
