export default function () {
  const isResearchQuery =
    window.location.search && window.location.search.includes("research");
  if (!isResearchQuery) return;
  const form = document.querySelector("#otrasli-nashi_raboty");
  if (!form) {
    console.log("Form not found");
  } else {
    setTimeout(() => {
      form.scrollIntoView();
      console.log("scroll");
    }, 1500);
  }
}
