import { FC, ReactNode } from 'react'
import { Grid, GridProps } from '../../Grid/components/grid'
import styles from './styles.module.scss'
export interface CardGridProps extends GridProps {
  name?: ReactNode
  children: ReactNode
  title?: string
}
export const CardGrid: FC<CardGridProps> = ({
  name = '',
  title = '',
  children,
  ...props
}) => (
  <Grid {...props}>
    <div className={styles.group}>
      {Boolean(name) && (
        <div className={styles.title} title={title}>
          {name}
        </div>
      )}
      <div className={styles.content}>{children}</div>
    </div>
  </Grid>
)
