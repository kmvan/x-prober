import type { FC, HTMLProps, ReactNode } from 'react';
import styles from './index.module.scss';interface UiDescriptionProps extends HTMLProps<HTMLDivElement> {
  items: {
    id: string;
    text: ReactNode;
  }[];
}
export const UiDescription: FC<UiDescriptionProps> = ({ items }) => (
  <ul className={styles.main}>
    {items.map(({ id, text }) => (
      <li className={styles.item} key={id}>
        {text}
      </li>
    ))}
  </ul>
);
