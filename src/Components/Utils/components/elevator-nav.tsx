import {
  Children,
  FC,
  ReactElement,
  useCallback,
  useEffect,
  useRef,
} from 'react'
interface ElevatorNavProps {
  activeIndex: number
  children: ReactElement[]
}
export const ElevatorNav: FC<ElevatorNavProps> = ({
  activeIndex,
  children,
}) => (
  <>
    {Children.map(children, (child, i) => {
      const isActive = activeIndex === i
      const { type: Component, props } = child as ReactElement
      const { className = '', ...p } = props
      return (
        <Component
          className={className}
          data-active={isActive || undefined}
          {...p}
        />
      )
    })}
  </>
)
interface ElevatorNavBodyProps {
  id: string
  setActiveIndex: (activeIndex: number) => void
  threshold?: number
  topOffset?: number
  children: ReactElement[]
}
export const ElevatorNavBody: FC<ElevatorNavBodyProps> = ({
  id,
  setActiveIndex,
  threshold = 50,
  topOffset = 50,
  children,
}) => {
  const position = useRef<[start: number, end: number][]>([[0, 0]])
  const timer = useRef<number>(0)
  const onScroll = useCallback(() => {
    if (timer.current) {
      window.clearTimeout(timer.current)
    }
    timer.current = window.setTimeout(() => {
      const y = Math.round(window.scrollY) + topOffset
      position.current.map(([start, end], i) => {
        if (y >= start && y < start + end) {
          return setActiveIndex(i)
        }
        return null
      })
    }, threshold)
  }, [setActiveIndex, threshold, topOffset])
  useEffect(() => {
    const resizeObserver = new ResizeObserver(() => {
      const count = Children.count(children)
      position.current = children.map((child, i) => {
        const element: HTMLElement | null = document.querySelector(
          `[data-elevator='${id}-${i}']`,
        )
        if (!element) {
          return [0, 0]
        }
        switch (i) {
          case 0:
            return [0, Math.round(element.offsetHeight)]
          case count - 1:
            return [
              Math.round(element.offsetTop),
              Math.round(document.body.offsetHeight),
            ]
          default:
            return [
              Math.round(element.offsetTop),
              Math.round(element.offsetHeight),
            ]
        }
      })
    })
    resizeObserver.observe(document.body)
    return () => resizeObserver.unobserve(document.body)
  }, [children, id])
  useEffect(() => {
    window.addEventListener('scroll', onScroll)
    return () => {
      window.removeEventListener('scroll', onScroll)
    }
  }, [onScroll])
  return (
    <>
      {Children.map(children, (child, i) => {
        const { type: Component, props } = child as ReactElement
        return <Component {...props} data-elevator={`${id}-${i}`} />
      })}
    </>
  )
}
