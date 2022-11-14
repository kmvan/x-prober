import { AnchorHTMLAttributes, FC } from 'react'
import styles from './styles.module.scss'
export const CardLink: FC<AnchorHTMLAttributes<HTMLAnchorElement>> = (
  props,
) => <a className={styles.link} target='_blank' {...props} />
