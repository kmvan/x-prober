class Main {
  getRndint = (min: number, max: number) => {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  };
  runJs = (): number => {
    const totalMs = 1000;
    let times = 0;
    const startTime = performance.now();
    for (;;) {
      for (let i = 0; i < 10_000; i++) {
        (Math.sqrt(i) * Math.sin(i)) / Math.tan(i + 1);
      }
      const arr = new Array(1000).fill(0).map((_, i) => i);
      arr.sort(() => Math.random() - 0.5);
      arr.sort((a, b) => a - b);
      times++;
      if (performance.now() - startTime > totalMs) {
        break;
      }
    }
    return times;
  };
  runDom = (): number => {
    const totalMs = 1000;
    let times = 0;
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    const startTime = performance.now();
    const container = document.createElement('div');
    container.style.cssText = `
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
`;
    document.body.appendChild(container);
    for (;;) {
      for (let i = 0; i < 100; i++) {
        const div = document.createElement('div');
        div.className = 'benchmark-dom';
        div.style.position = 'fixed';
        div.style.left = '0px';
        div.style.top = '0px';
        div.style.width = `${this.getRndint(50, screenWidth)}px`;
        div.style.height = `${this.getRndint(50, screenHeight)}px`;
        div.style.border = '1px solid green';
        container.appendChild(div);
      }
      // query
      const eles = document.querySelectorAll(
        '.benchmark-dom'
      ) as NodeListOf<HTMLDivElement>;
      for (const ele of Array.from(eles)) {
        ele.style.borderColor = 'red';
      }
      while (container.firstChild) {
        container.removeChild(container.firstChild);
      }
      times++;
      if (performance.now() - startTime > totalMs) {
        break;
      }
    }
    document.body.removeChild(container);
    return times;
  };
  runCanvas = () => {
    const totalMs = 1000;
    let times = 0;
    const canvas = document.createElement('canvas');
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    canvas.width = screenWidth;
    canvas.height = screenHeight;
    canvas.style.position = 'fixed';
    canvas.style.top = '0px';
    canvas.style.left = '0px';
    document.body.appendChild(canvas);
    const ctx = canvas.getContext('2d') as CanvasRenderingContext2D;
    const startTime = performance.now();
    for (;;) {
      for (let i = 0; i < 100; i++) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const halfSize = canvas.width / 2;
        ctx.lineWidth = this.getRndint(1, 5);
        ctx.strokeStyle = 'red';
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.moveTo(centerX - halfSize, centerY - halfSize);
        ctx.lineTo(centerX + halfSize, centerY + halfSize);
        ctx.rotate(this.getRndint(0, 360));
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(centerX + halfSize, centerY - halfSize);
        ctx.lineTo(centerX - halfSize, centerY + halfSize);
        ctx.rotate(this.getRndint(0, 360));
        ctx.stroke();
      }
      times++;
      if (performance.now() - startTime > totalMs) {
        break;
      }
    }
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.body.removeChild(canvas);
    return times;
  };
}
export const BrowserBenchmarkTests = new Main();
