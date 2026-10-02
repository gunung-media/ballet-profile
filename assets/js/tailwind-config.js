/* Konfigurasi Tailwind bersama untuk website & panel admin (dimuat setelah Tailwind CDN). */
tailwind.config = {
  theme: {
    extend: {
      colors: {
        ivory: '#FBF7F1',
        cream: '#F3ECE2',
        blush: '#F4E1DE',
        rose: { DEFAULT: '#B9838A', deep: '#8E5E66' },
        champagne: '#C6A66B',
        charcoal: '#2F2A2B',
      },
      fontFamily: {
        serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
};
