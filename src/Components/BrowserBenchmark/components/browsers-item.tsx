import copyToClipboard from 'copy-to-clipboard';
import type { FC, MouseEvent, ReactNode } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { template } from '@/Components/Utils/components/template.ts';
import { UiRuby } from '@/Components/ui/ruby/index.tsx';
import { BrowserBenchmarkMarksMeter } from './marks-meter.tsx';
import styles from './server-item.module.scss';
import type { BrowserBenchmarkMarksProps } from './typings.ts';

const BrowserBenchmarkResult: FC<{
  sunSpider: number;
  hash: number;
  object: number;
  cssAnimation: number;
  gc: number;
  canvas: number;
  webgl: number;
  date?: string;
}> = ({ sunSpider, hash, object, cssAnimation, gc, canvas, webgl, date }) => {
  const total = sunSpider + hash + object + cssAnimation + gc + canvas + webgl;
  const sunSpiderString = sunSpider.toLocaleString();
  const hashString = hash.toLocaleString();
  const objectString = object.toLocaleString();
  const cssAnimationString = cssAnimation.toLocaleString();
  const gcString = gc.toLocaleString();
  const canvasString = canvas.toLocaleString();
  const webglString = webgl.toLocaleString();
  const totalString = total.toLocaleString();
  const totalText = template(
    '{{sunSpider}} (SunSpider) + {{hash}} (Hash) + {{object}} (Object) + {{cssAnimation}} (CSS Animation) + {{gc}} (GC) + {{canvas}} (Canvas) + {{webgl}} (WebGL) = {{total}}',
    {
      sunSpider: sunSpiderString,
      hash: hashString,
      object: objectString,
      cssAnimation: cssAnimationString,
      gc: gcString,
      canvas: canvasString,
      webgl: webglString,
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
      <UiRuby rt="SunSpider" ruby={sunSpiderString} />
      {sign}
      <UiRuby rt="Hash" ruby={hashString} />
      {sign}
      <UiRuby rt="Object" ruby={objectString} />
      {sign}
      <UiRuby rt="CSS Animation" ruby={cssAnimationString} />
      {sign}
      <UiRuby rt="GC" ruby={gcString} />
      {sign}
      <UiRuby rt="Canvas" ruby={canvasString} />
      {sign}
      <UiRuby rt="WebGL" ruby={webglString} />
      {sign}
      <span className={styles.sign}>=</span>
      <UiRuby isResult rt={date || ''} ruby={totalString} />
    </button>
  );
};
export const BrowserBenchmarkItem: FC<{
  header: ReactNode;
  marks: BrowserBenchmarkMarksProps;
  maxMarks: number;
  date: string;
}> = ({ header, marks, maxMarks, date }) => {
  const { sunSpider, hash, object, cssAnimation, gc, canvas, webgl } = marks;
  return (
    <div className={styles.main}>
      <div className={styles.header}>{header}</div>
      <BrowserBenchmarkResult
        canvas={canvas}
        cssAnimation={cssAnimation}
        date={date}
        gc={gc}
        hash={hash}
        object={object}
        sunSpider={sunSpider}
        webgl={webgl}
      />
      <BrowserBenchmarkMarksMeter
        total={sunSpider + hash + object + cssAnimation + gc + canvas + webgl}
        totalMarks={maxMarks}
      />
    </div>
  );
};
