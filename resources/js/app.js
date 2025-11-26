import "./bootstrap";
import { createIcons } from "lucide";
import * as icons from "lucide";
import $ from "jquery";
import Alpine from "alpinejs";
import { Toast } from "./utils/daisy_toast";
import { cartState } from "./state/cart_state";
import { wishlistState } from "./state/wishlist_state";
import { notificationState } from "./state/notification_state";
import * as GeneralHelper from "./utils/general_utils";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import ApexCharts from "apexcharts";
import Quill from "quill";
import "quill/dist/quill.snow.css";

createIcons({ icons });
dayjs.extend(relativeTime);

window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.Toast = Toast;
window.GeneralHelper = GeneralHelper;
window.dayjs = dayjs;
window.ApexCharts = ApexCharts;
window.Quill = Quill;

Alpine.store("cart", cartState);
Alpine.store("wishlist", wishlistState);
Alpine.store("notification", notificationState);
Alpine.start(); // required line
