import { FC, HTMLProps } from 'react'
import styles from './styles.module.scss'
const LoadingIcon = () => (
  <svg
    width='16px'
    height='16px'
    viewBox='0 0 100 100'
    preserveAspectRatio='xMidYMid'
  >
    <g transform='translate(50 50)'>
      <g transform='scale(0.7)'>
        <g transform='translate(-50 -50)'>
          <g>
            <animateTransform
              attributeName='transform'
              type='rotate'
              repeatCount='indefinite'
              values='0 50 50;360 50 50'
              keyTimes='0;1'
              dur='0.7575757575757576s'
            />
            <path
              fillOpacity='0.8'
              fill='#832f0e'
              d='M50 50L50 0A50 50 0 0 1 100 50Z'
            />
          </g>
          <g>
            <animateTransform
              attributeName='transform'
              type='rotate'
              repeatCount='indefinite'
              values='0 50 50;360 50 50'
              keyTimes='0;1'
              dur='1.0101010101010102s'
            />
            <path
              fillOpacity='0.8'
              fill='#0c0a08'
              d='M50 50L50 0A50 50 0 0 1 100 50Z'
              transform='rotate(90 50 50)'
            />
          </g>
          <g>
            <animateTransform
              attributeName='transform'
              type='rotate'
              repeatCount='indefinite'
              values='0 50 50;360 50 50'
              keyTimes='0;1'
              dur='1.5151515151515151s'
            />
            <path
              fillOpacity='0.8'
              fill='#594a40'
              d='M50 50L50 0A50 50 0 0 1 100 50Z'
              transform='rotate(180 50 50)'
            />
          </g>
          <g>
            <animateTransform
              attributeName='transform'
              type='rotate'
              repeatCount='indefinite'
              values='0 50 50;360 50 50'
              keyTimes='0;1'
              dur='3.0303030303030303s'
            />
            <path
              fillOpacity='0.8'
              fill='#8e7967'
              d='M50 50L50 0A50 50 0 0 1 100 50Z'
              transform='rotate(270 50 50)'
            />
          </g>
        </g>
      </g>
    </g>
  </svg>
)
export const Loading: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.main}>
    <LoadingIcon />
    <div className={styles.text} {...props} />
  </div>
)
