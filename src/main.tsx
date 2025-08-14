import { createRoot } from 'react-dom/client';
import { Bootstrap } from './Components/Bootstrap/components/index.tsx';

document.addEventListener('DOMContentLoaded', () => {
  document.body.innerHTML = '';
  createRoot(document.body).render(<Bootstrap />);
});
