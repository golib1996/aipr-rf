import catalog from './catalog-list';
import sendEmail from './send-email.js';
import catForm from './category-form.js';
import stickyTable from './sticky-table.js';

function DOMLoaded() {
  catalog();
  sendEmail();
  catForm();
  stickyTable();
}

document.addEventListener("DOMContentLoaded", DOMLoaded);