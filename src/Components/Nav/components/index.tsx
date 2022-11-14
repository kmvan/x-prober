import { observer } from 'mobx-react-lite'
import { FC, ReactElement } from 'react'
import { CardStore } from '../../Card/stores'
import { ElevatorNav } from '../../Utils/components/elevator-nav'
import { NavStore } from '../stores'
import styles from './styles.module.scss'
export const Nav: FC = observer(() => {
  const items = CardStore.enabledCards
    .map(({ id, title, tinyTitle, enabled = true }) => {
      if (!enabled) {
        return null
      }
      return (
        <a className={styles.link} key={id} href={`#${id}`}>
          <span className={styles.linkTitle}>{title}</span>
          <span className={styles.linkTitleTiny}>{tinyTitle}</span>
        </a>
      )
    })
    .filter((n) => n) as ReactElement[]
  return (
    <div className={styles.main}>
      <ElevatorNav activeIndex={NavStore.activeIndex}>{items}</ElevatorNav>
    </div>
  )
})
