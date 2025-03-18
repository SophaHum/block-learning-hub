/**
 * Format a date string into a readable format
 *
 * @param {string} dateString - The date string to format
 * @returns {string} - Formatted date string
 */
export const formatDate = (dateString) => {
  if (!dateString) return '';

  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

/**
 * Truncate text to a specific length
 *
 * @param {string} text - The text to truncate
 * @param {number} length - Maximum length
 * @returns {string} - Truncated text
 */
export const truncate = (text, length = 100) => {
  if (!text || text.length <= length) return text;

  return text.substring(0, length) + '...';
};

/**
 * Updates a ref's value using either a direct value or an updater function
 * Designed for use with TanStack Table's state management
 */
export const valueUpdater = (updaterOrValue, ref) => {
  if (typeof updaterOrValue === 'function') {
    ref.value = updaterOrValue(ref.value)
  } else {
    ref.value = updaterOrValue
  }
}
