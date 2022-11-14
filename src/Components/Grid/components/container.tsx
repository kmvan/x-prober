import { FC, HTMLProps } from 'react'
import styles from './styles.module.scss'
export const GridContainer: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.container} {...props} />
)
