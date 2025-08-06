import type { FC } from 'react';
import styles from './index.module.scss';

interface SearchLinkProps {
  keyword: string;
}
export const SearchLink: FC<SearchLinkProps> = ({ keyword }) => (
  <a
    className={styles.main}
    href={`https://www.google.com/search?q=php+${encodeURIComponent(keyword)}`}
    rel="nofollow noreferrer"
    target="_blank"
  >
    {keyword}
  </a>
);
