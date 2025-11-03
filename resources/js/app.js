import "./bootstrap";
import { createIcons } from "lucide";
import * as icons from "lucide";
import $ from "jquery";
import Alpine from "alpinejs";
import { Toast } from "./utils/daisy_toast";
import { cartState } from "./state/cart_state";
import * as GeneralHelper from "./utils/general_utils";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";

createIcons({ icons });
dayjs.extend(relativeTime);

window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.Toast = Toast;
window.GeneralHelper = GeneralHelper;
window.dayjs = dayjs;


Alpine.store("cart", cartState);
Alpine.start(); // required line
Alpine.debug = true;
