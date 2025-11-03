export function capitalize(str) {
    if (!str || typeof str !== "string") return "";
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

export function formatPrice(price) {
    return `$${Number(price).toFixed(2)}`;
}

export function toTitleCase(str) {
  return  str.toLowerCase()
        .split(" ")
        .filter(Boolean)
        .map((word) => word[0].toUpperCase() + word.slice(1))
        .join(" ");
}
