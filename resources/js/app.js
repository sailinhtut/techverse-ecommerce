import "./bootstrap";
import { createIcons } from "lucide";
import * as icons from "lucide";
import $ from "jquery";
import Alpine from "alpinejs";
import { Toast } from "./utils/daisy_toast";
import { cartState } from "./state/cart_state";

createIcons({ icons });

window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.Toast = Toast;

Alpine.store("cart", cartState);
Alpine.start(); // required line
