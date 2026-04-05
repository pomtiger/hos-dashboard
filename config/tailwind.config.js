/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.php",
    "./layouts/**/*.php",
    "./widgets/**/*.php",
    "./assets/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        'hos-blue': '#005b96',
      },
      // เพิ่มส่วนนี้เข้าไป
      fontFamily: {
        'kanit': ['Kanit', 'sans-serif'],   // ตั้งเป็นฟอนต์หลักของทั้งเว็บ
        'mitr': ['Mitr', 'sans-serif'], // เอาไว้ใช้เฉพาะจุดที่อยากให้เด่น 
      },
    },
  },
  plugins: [],
}