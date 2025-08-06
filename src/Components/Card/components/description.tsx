import type { FC, HTMLProps, ReactNode } from 'react';
import styles from './description.module.scss';

interface CardDescriptionProps extends HTMLProps<HTMLDivElement> {
  items: {
    id: string;
    text: ReactNode;
  }[];
}
export const CardDescription: FC<CardDescriptionProps> = ({ items }) => (
  <ul className={styles.main}>
    {items.map(({ id, text }) => (
      <li className={styles.item} key={id}>
        {text}
      </li>
    ))}
  </ul>
);
