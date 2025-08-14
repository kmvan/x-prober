import type { FC } from 'react';
import styles from './index.module.scss';export const SearchLink: FC<{
  keyword: string;
}> = ({ keyword }) => (
  <a
    className={styles.main}
    href={`https://www.google.com/search?q=php+${encodeURIComponent(keyword)}`}
    rel="nofollow noreferrer"
    target="_blank"
  >
    {keyword}
  </a>
);
