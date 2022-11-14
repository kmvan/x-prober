import { FC } from 'react'
import styles from './styles.module.scss'
interface SearchLinkProps {
  keyword: string
}
export const SearchLink: FC<SearchLinkProps> = ({ keyword }) => (
  <a
    className={styles.main}
    href={`https://www.google.com/search?q=php+${encodeURIComponent(keyword)}`}
    target='_blank'
    rel='nofollow noreferrer'
  >
    {keyword}
  </a>
)
