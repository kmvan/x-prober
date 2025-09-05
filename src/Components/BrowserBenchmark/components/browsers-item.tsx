import copyToClipboard from 'copy-to-clipboard';
import type { FC, MouseEvent, ReactNode } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { template } from '@/Components/Utils/components/template.ts';
import { UiRuby } from '@/Components/ui/ruby/index.tsx';
import styles from './browsers-item.module.scss';
import { BrowserBenchmarkMarksMeter } from './marks-meter.tsx';
import type { BrowserBenchmarkMarksProps } from './typings.ts';

const BrowserBenchmarkResult: FC<{
  js: number;
  dom: number;
  canvas: number;
  date?: string;
}> = ({ js, dom, canvas, date }) => {
  const total = js + dom + canvas;
  const jsString = js.toLocaleString();
  const domString = dom.toLocaleString();
  const canvasString = canvas.toLocaleString();
  const totalString = total.toLocaleString();
  const totalText = template(
    '{{js}} (JS) + {{dom}} (DOM) + {{canvas}} (Canvas) = {{total}}',
    {
      js: jsString,
      dom: domString,
      canvas: canvasString,
      total: totalString,
    }
  );
  const sign = <span className={styles.sign}>+</span>;
  const handleCopyMarks = (e: MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();
    e.stopPropagation();
    copyToClipboard(totalText);
  };
  return (
    <button
      className={styles.marks}
      onClick={handleCopyMarks}
      title={gettext('Touch to copy marks')}
      type="button"
    >
      <UiRuby rt="JS" ruby={jsString} />
      {sign}
      <UiRuby rt="DOM" ruby={domString} />
      {sign}
      <UiRuby rt="Canvas" ruby={canvasString} />
      {sign}
      <span className={styles.sign}>=</span>
      <UiRuby isResult rt={date || ''} ruby={totalString} />
    </button>
  );
};
export const BrowserBenchmarkItem: FC<{
  ua: string;
  header: ReactNode;
  marks: BrowserBenchmarkMarksProps;
  maxMarks: number;
  date: string;
}> = ({ ua, header, marks, maxMarks, date }) => {
  const { js, dom, canvas } = marks;
  return (
    <div className={styles.main}>
      <div className={styles.header} title={ua}>
        {header}
      </div>
      <BrowserBenchmarkResult canvas={canvas} date={date} dom={dom} js={js} />
      <BrowserBenchmarkMarksMeter
        total={js + dom + canvas}
        totalMarks={maxMarks}
      />
    </div>
  );
};
