declare interface ResizeObserver {
  observe(target: Element): void
  unobserve(target: Element): void
  disconnect(): void
}

declare var ResizeObserver: {
  prototype: ResizeObserver
  new (callback: ResizeObserverCallback): ResizeObserver
}

interface ResizeObserverSize {
  inlineSize: number
  blockSize: number
}

type ResizeObserverCallback = (
  entries: ReadonlyArray<ResizeObserverEntry>,
  observer: ResizeObserver
) => void

interface ResizeObserverEntry {
  readonly target: Element
  readonly contentRect: DOMRectReadOnly
  readonly borderBoxSize: ResizeObserverSize
  readonly contentBoxSize: ResizeObserverSize
}
